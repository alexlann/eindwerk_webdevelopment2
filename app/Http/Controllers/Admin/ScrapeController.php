<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Scrape;
use Goutte\Client;
use Illuminate\Http\Request;
use stdClass;

class ScrapeController extends Controller
{
    private $categoryUrls = [
        "babycompany" => "https://www.babycompany.be/kledij-meisjes-zomer",
        "helloteddy" => "https://www.helloteddy.be",
        "babydump" => "https://www.baby-dump.be",
    ];

    private $shops = [
        "babycompany" => "Babycompany",
        "helloteddy" => "Hello Teddy",
        "babydump" => "Baby-Dump",
    ];

    public function index() {
        if(auth()->user()->isAdmin) {
            $shops = $this->shops;

            // build query
            $query = Category::where("title", "LIKE", "%");
            if(session("shop")) {
                $query = $query->where("shop", "=", session("shop"));
                $filteredShop = $shops[session("shop")];
            } else {
                $filteredShop = NULL;
            }
            $categories = $query->get();

            // calculate how long ago last scrape was
            foreach($categories as $category) {
                $categoryScrapedOn = Category::where("url", "=", $category->url)->first(["scraped_on"])->scraped_on;
                if($categoryScrapedOn !== NULL) {
                    $date1 = strtotime($categoryScrapedOn);
                    $date2 = time();
                    $category->days_between = Scrape::convertTimeString((int)(($date2 - $date1)/86400));

                } else {
                    $category->days_between = "...";
                }
            }

            // show admin menu button
            $header["menu"] = true;

            // return view
            return view("scrape", compact("shops", "header", "categories", "filteredShop"));
        } else {
            return redirect()->route("products.index");
        }
    }

    public function prices() {
        if(auth()->user()->isAdmin) {
            // get categories
            $categories = Category::get();

            // calculate how long ago last scrape was
            foreach($categories as $category) {
                $categoryScraped = Category::where("url", "=", $category->url)->first(["scraped_on"]);
                if($categoryScraped->scraped_on !== NULL) {
                    $date1 = strtotime($categoryScraped->scraped_on);
                    $date2 = time();
                    $category->days_between = Scrape::convertTimeString((int)(($date2 - $date1)/86400));
                } else {
                    $category->days_between = NULL;
                }
            }

            // show admin menu button
            $header["menu"] = true;

            // return view
            return view("scrape-price", compact("header", "categories"));
        } else {
            return redirect()->route("products.index");
        }
    }

    public function scrapeCategories(Request $r) {
        if(auth()->user()->isAdmin) {
            switch($r->shop) {
                case "babycompany" :
                    return $this->scrapeBabycompanyCategories($this->categoryUrls[$r->shop], $r->shop);
                    break;
                case "helloteddy" :
                    return $this->scrapeHelloTeddyCategories($this->categoryUrls[$r->shop], $r->shop);
                    break;
                case "babydump" :
                    return $this->scrapeBabyDumpCategories($this->categoryUrls[$r->shop], $r->shop);
                    break;
            }
        } else {
            return redirect()->route("products.index");
        }
    }

    public function scrapeProducts(Request $r) {
        if(auth()->user()->isAdmin) {
            $category = Category::where("id", "=", $r->category_id)->firstOrFail();
            // check shop
            switch($category->shop) {
                case "babycompany" :
                    return $this->scrapeBabycompanyProducts($category->url, $category->shop, $category->id);
                    break;
                case "helloteddy" :
                    return $this->scrapeHelloTeddyProducts($category->url, $category->shop, $category->id);
                    break;
                case "babydump" :
                    return $this->scrapeBabyDumpProducts($category->url, $category->shop, $category->id);
                    break;
            }
        } else {
            return redirect()->route("products.index");
        }
    }

    public function scrapePrices(Request $r) {
        if(auth()->user()->isAdmin) {
            $category = Category::where("id", "=", $r->category_id)->firstOrFail();
            // check shop
            switch($category->shop) {
                case "babycompany" :
                    return $this->scrapeBabycompanyProducts($category->url, $category->shop, $category->id);
                    break;
                case "helloteddy" :
                    return $this->scrapeHelloTeddyProducts($category->url, $category->shop, $category->id);
                    break;
                case "babydump" :
                    return $this->scrapeBabyDumpProducts($category->url, $category->shop, $category->id);
                    break;
            }
        } else {
            return redirect()->route("products.index");
        }
    }

    /** BABYCOMPANY */
    private function scrapeBabycompanyCategories($url, $shop) {
        $client = new Client();
        $crawler = $client->request("GET", $url);
        $this->shop = $shop;

        $categories = $crawler->filter(".content .page-content .row .content-aside .content-aside-inner .block .block_content .tree li a")
            ->each(function($node) {
                $title = str_replace(" nieuwe collectie", "", ucfirst(strtolower($node->text())));
                $url = $node->attr("href");

                $cat = new stdClass();
                $cat->title = $title;
                $cat->shop = $this->shop;
                $cat->url = $url;

                return $cat;
            });

        foreach($categories as $scrapeCategory) {
            // check if exists
            if(Category::where([["title", "=", $scrapeCategory->title]])->exists()) continue;

            // create/add category to database
            $categoryEntity = new Category();
            $categoryEntity->title = $scrapeCategory->title;
            $categoryEntity->url = $scrapeCategory->url;
            $categoryEntity->shop = $scrapeCategory->shop;
            $categoryEntity->save();
        }

        return redirect()->back()->with("status", __("Categories scraped"));
    }

    private function scrapeBabycompanyProducts($url, $shop, $categoryId) {
        $client = new Client();
        $crawler = $client->request("GET", $url);

        $this->scrapeBabycompanyPageData($crawler, $shop, $url, $categoryId);

        for($i = 0; $i <= 10; $i++) {
            $crawler = Scrape::getNextPage($crawler, ".pagination_next a");
            if(!$crawler) break;
            $this->scrapeBabycompanyPageData($crawler, $shop, $url, $categoryId);
        };

        return redirect()->back()->with("status", __("Products scraped"));
    }

    private function scrapeBabycompanyPageData($crawler, $shop, $url, $categoryId) {
        $this->shop = $shop;
        $this->scrape_url = $url;
        $this->category = Category::where("id", "=", $categoryId)->firstOrFail();

        $crawler->filter(".product-preview ")->each(function($node) {
            //check if item already exists
            if(!Product::where("unique_id", "=", $node->filter(".preview a")->first()->attr("data-id-product"))->exists()) {
                $product = new Product();
                $product->title = ucfirst(strtolower($node->filter(".product-info a")->first()->text()));
                $product->price = floatval(str_replace(",", ".", mb_substr($node->filter(".product-info .content_price")->first()->text(), 2)));
                $product->detail_url = $node->filter(".product-info a")->first()->attr("href");
                $product->scrape_url = $this->scrape_url;
                $product->store = $this->shop;
                $product->unique_id = $node->filter(".preview a")->first()->attr("data-id-product");
                $product->save();

                $this->productId = $product->id;

                Scrape::getNextPage($node, ".preview .product-image")->filter(".slides li a")->each(function($imageNode) {
                    $imagePath = $imageNode->attr("href");

                    // save image in storage
                    $fullPath = Scrape::createImageName($imagePath);

                    // save full path name in db
                    $productImage = new ProductImage();
                    $productImage->name = $fullPath;
                    $productImage->product_id = $this->productId;

                    $productImage->save();
                });

                $product->categories()->attach([$this->category->id]);
                $product->save();

                $this->category->scraped_on = now();
                $this->category->save();
            } else {
                $product = Product::where("unique_id", "=", $node->filter(".preview a")->first()->attr("data-id-product"))->firstOrFail();
                $product->price = floatval(str_replace(",", ".", mb_substr($node->filter(".product-info .content_price")->first()->text(), 2)));
                $product->save();

                $this->category->scraped_on = now();
                $this->category->save();
            }
        });
    }

    /** HelloTeddy */
    private function scrapeHelloTeddyCategories($url, $shop) {
        $client = new Client();
        $crawler = $client->request("GET", $url);
        $this->shop = $shop;

        $categories = $crawler->filter(".shopify-section .navigation-has-mega-nav ul .has-mega-nav .mega-nav .mega-nav-list-wrapper .mega-nav-list li a")
            ->each(function($node) {
                $title = $node->text();
                $url = $node->attr("href");

                $cat = new stdClass();
                $cat->title = $title;
                $cat->shop = $this->shop;
                $cat->url = $this->categoryUrls["helloteddy"] . $url;

                return $cat;
            });

        foreach($categories as $scrapeCategory) {
            // check if exists
            if(Category::where([["title", "=", $scrapeCategory->title]])->exists()) continue;

            // create/add category to database
            $categoryEntity = new Category();
            $categoryEntity->title = $scrapeCategory->title;
            $categoryEntity->url = $scrapeCategory->url;
            $categoryEntity->shop = $scrapeCategory->shop;
            $categoryEntity->save();
        }

        return redirect()->back()->with("status", __("Categories scraped"));
    }

    private function scrapeHelloTeddyProducts($url, $shop, $categoryId) {
        $client = new Client();
        $crawler = $client->request("GET", $url);

        $this->scrapeHelloTeddyPageData($crawler, $shop, $url, $categoryId);

        for($i = 0; $i <= 10; $i++) {
            $crawler = Scrape::getNextPage($crawler, ".pagination .next a");
            if(!$crawler) break;
            $this->scrapeHelloTeddyPageData($crawler, $shop, $url, $categoryId);
        };

        return redirect()->back()->with("status", __("Products scraped"));
    }

    private function scrapeHelloTeddyPageData($crawler, $shop, $url, $categoryId) {
        $this->shop = $shop;
        $this->scrape_url = $url;
        $this->category = Category::where("id", "=", $categoryId)->firstOrFail();

        $crawler->filter(".product-list-item")->each(function($node) {
            //check if item already exists
            if(!Product::where("unique_id", "=", $node->attr("data-product-id"))->exists()) {
                $toDetailPage = ".product-list-item-title a";

                $product = new Product();
                $product->title = $node->filter(".product-list-item-title a")->first()->text();
                $product->price = floatval(mb_substr($node->filter(".product-list-item-price .money")->first()->text(), 1));
                $product->detail_url = $this->categoryUrls["helloteddy"] . $node->filter($toDetailPage)->first()->attr("href");
                $product->scrape_url = $this->scrape_url;
                $product->store = $this->shop;
                $product->description = Scrape::getNextPage($node, $toDetailPage)->filter(".product-description")->first()->text();
                $product->unique_id = $node->attr("data-product-id");
                $product->save();

                $this->productId = $product->id;

                // save main image if there is only one image of the product
                if(Scrape::getNextPage($node, $toDetailPage)->filter(".product-thumbnails img")->first()->count() > 0) {
                    Scrape::getNextPage($node, $toDetailPage)->filter(".product-thumbnails img")->each(function($imageNode) {
                        // No link available to click on with crawler to enlarge photo
                        $this->saveImageDataHelloTeddy($imageNode);
                    });
                } else {
                    $imageNode = Scrape::getNextPage($node, $toDetailPage)->filter(".product-main-image img")->first();
                    $this->saveImageDataHelloTeddy($imageNode);
                }

                $product->categories()->attach([$this->category->id]);
                $product->save();

                $this->category->scraped_on = now();
                $this->category->save();
            } else {
                $product = Product::where("unique_id", "=", $node->attr("data-product-id"))->firstOrFail();
                $product->price = floatval(mb_substr($node->filter(".product-list-item-price .money")->first()->text(), 1));
                $product->save();

                $this->category->scraped_on = now();
                $this->category->save();
            }
        });
    }

    private function saveImageDataHelloTeddy($node) {
        $roughImageName = explode("?", $node->attr("src"));
        $imagePath = "http:" . $roughImageName[0];

        // save image in storage
        $fullPath = Scrape::createImageName($imagePath);

        // save full path name in db
        $productImage = new ProductImage();
        $productImage->name = $fullPath;
        $productImage->product_id = $this->productId;

        $productImage->save();
    }

    /** BabyDump */
    private function scrapeBabyDumpCategories($url, $shop) {
        $client = new Client();
        $crawler = $client->request("GET", $url);
        $this->shop = $shop;

        $categories = $crawler->filter(".submenu-content .categories ul li a")
            ->each(function($node) {
                $title = $node->text();
                $url = $node->attr("href");

                $cat = new stdClass();
                $cat->title = $title;
                $cat->shop = $this->shop;
                $cat->url = $url;

                return $cat;
            });

        foreach($categories as $scrapeCategory) {
            // check if exists
            if(Category::where([["title", "=", $scrapeCategory->title]])->exists()) continue;

            // create/add category to database
            $categoryEntity = new Category();
            $categoryEntity->title = $scrapeCategory->title;
            $categoryEntity->url = $scrapeCategory->url;
            $categoryEntity->shop = $scrapeCategory->shop;
            $categoryEntity->save();
        }

        return redirect()->back()->with("status", __("Categories scraped"));
    }

    private function scrapeBabyDumpProducts($url, $shop, $categoryId) {
        $client = new Client();
        $crawler = $client->request("GET", $url);

        $this->scrapeBabyDumpPageData($crawler, $shop, $url, $categoryId);

        return redirect()->back()->with("status", __("Products scraped"));
    }

    private function scrapeBabyDumpPageData($crawler, $shop, $url, $categoryId) {
        $this->shop = $shop;
        $this->url = $url;
        $this->categoryId = $categoryId;
        $this->toDetailPage = ".product-name a";

        $crawler->filter(".product-list .item")->each(function($node) {
            // check if there is an overview page behind first link
            if(Scrape::getNextPage($node, $this->toDetailPage)->filter(".col-xs-12 .page-title .article-code")->first()->count() <= 0) {
                // get products from new overview page
                Scrape::getNextPage($node, $this->toDetailPage)->filter(".product-list .item")->each(function($subnode) {

                    // check if there is another overview page behind first link
                    if(Scrape::getNextPage($subnode, $this->toDetailPage)->filter(".col-xs-12 .page-title .article-code")->first()->count() <= 0) {
                        // get products from new overview page
                            Scrape::getNextPage($subnode, $this->toDetailPage)->filter(".product-list .item")->each(function($subsubnode) {

                                // check if item has valid price (only needed for baby-dump)
                                if($subsubnode->filter(".item-order-info")->text() != "") {
                                    // check if product is unique
                                    if (!Product::where("unique_id", "=", Scrape::getNextPage($subsubnode, $this->toDetailPage)->filter(".col-xs-12 .page-title .article-code")->first()->text())->exists()) {
                                        $this->saveBabyDumpData($subsubnode, $this->url, $this->categoryId);
                                    } else {
                                        $product = Product::where("unique_id", "=", Scrape::getNextPage($subsubnode, $this->toDetailPage)->filter(".col-xs-12 .page-title .article-code")->first()->text())->firstOrFail();
                                        $product->price = floatval(str_replace("-", "00", str_replace(",", ".", $subsubnode->filter(".price")->first()->text())));
                                        $product->save();

                                        $category = Category::where("id", "=", $this->categoryId)->firstOrFail();
                                        $category->scraped_on = now();
                                        $category->save();
                                    }
                                };
                            });
                    } else {
                        // check if item has valid price (only needed for baby-dump)
                        if($subnode->filter(".item-order-info")->text() != "") {
                            // check if product is unique
                            if (!Product::where("unique_id", "=", Scrape::getNextPage($subnode, $this->toDetailPage)->filter(".col-xs-12 .page-title .article-code")->first()->text())->exists()) {
                                $this->saveBabyDumpData($subnode, $this->url, $this->categoryId);
                            } else {
                                $product = Product::where("unique_id", "=", Scrape::getNextPage($subnode, $this->toDetailPage)->filter(".col-xs-12 .page-title .article-code")->first()->text())->firstOrFail();
                                $product->price = floatval(str_replace("-", "00", str_replace(",", ".", $subnode->filter(".price")->first()->text())));
                                $product->save();

                                $category = Category::where("id", "=", $this->categoryId)->firstOrFail();
                                $category->scraped_on = now();
                                $category->save();
                            }
                        };
                    }
                });
            // check if product is unique
            } elseif(!Product::where("unique_id", "=", Scrape::getNextPage($node, $this->toDetailPage)->filter(".col-xs-12 .page-title .article-code")->first()->text())->exists()) {
                $this->saveBabyDumpData($node, $this->url, $this->categoryId);
            } else {
                $product = Product::where("unique_id", "=", Scrape::getNextPage($node, $this->toDetailPage)->filter(".col-xs-12 .page-title .article-code")->first()->text())->firstOrFail();
                $product->price = floatval(str_replace("-", "00", str_replace(",", ".", $node->filter(".price")->first()->text())));
                $product->save();

                $category = Category::where("id", "=", $this->categoryId)->firstOrFail();
                $category->scraped_on = now();
                $category->save();
            }
        });
    }

    private function saveBabyDumpData($node, $scrape_url, $categoryId) {
        $toDetailPage = ".product-name a";

        $product = new Product();
        // check how to form detail_url
        if (str_contains($node->filter($toDetailPage)->first()->attr("href"), $this->categoryUrls["babydump"])) {
            $detail_url = $node->filter($toDetailPage)->first()->attr("href");
        } else {
            $detail_url = $this->categoryUrls["babydump"] . $node->filter($toDetailPage)->first()->attr("href");
        }
        $product->title = $node->filter($toDetailPage)->first()->text();
        $product->price = floatval(str_replace("-", "00", str_replace(",", ".", $node->filter(".price")->first()->text())));
        $product->detail_url = $detail_url;
        $product->scrape_url = $scrape_url;
        $product->store = $this->shop;
        $category = Category::where("id", "=", $categoryId)->firstOrFail();
        $detailBabyDumpPage = Scrape::getNextPage($node, $toDetailPage);
        $product->unique_id = $detailBabyDumpPage->filter(".col-xs-12 .page-title .article-code")->first()->text();
        $product->description = $detailBabyDumpPage->filter(".product-description-content p")->eq(1)->text();
        $product->save();

        $this->productId = $product->id;

        // save main image if there is only one image of the product
        if(Scrape::getNextPage($node, $toDetailPage)->filter(".thumbnails img")->first()->count() > 0) {
            Scrape::getNextPage($node, $toDetailPage)->filter(".thumbnails li")->each(function($imageNode) {
                // skip video's
                if($imageNode->attr("class") !== "video_thumb") {
                    $imageNode = Scrape::getNextPage($imageNode, "a")->filter(".easyzoom img");
                    $this->saveImageDataBabyDump($imageNode);
                }
            });
        } else {
            $imageNode = Scrape::getNextPage($node, $toDetailPage)->filter(".product-img img")->first();
            $this->saveImageDataBabyDump($imageNode);
        }

        $product->categories()->attach([$categoryId]);
        $product->save();

        $category->scraped_on = now();
        $category->save();

    }

    private function saveImageDataBabyDump($node) {
        $imagePath = $node->attr("src");

        // save image in storage
        $fullPath = Scrape::createImageName($imagePath);

        // save full path name in db
        $productImage = new ProductImage();
        $productImage->name = $fullPath;
        $productImage->product_id = $this->productId;

        $productImage->save();
    }
}
