<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Savedproduct;
use App\Models\Visitor;
use App\Models\Wishlist;
use COM;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $shops = [
        "babycompany" => "Babycompany",
        "helloteddy" => "Hello Teddy",
        "babydump" => "Baby-Dump",
    ];

    public function index($category_id = NULL) {
        // check if user is admin -> set right menu button
        auth()->user()->isAdmin ? $header["menu"] = true : $header["wishlist"] = true;

        // check if user already has wishlist and redirect if necessary
        if(!auth()->user()->isAdmin && !Wishlist::where("user_id", "=", auth()->user()->id)->first()) {
            return redirect("/wishlist/edit");
        }

        $shops = $this->shops;
        $categories = Category::orderBy("title", "asc")->get();
        $this->category_id = $category_id;

        // build query
        $filters = [];
        $query = Product::where("title", "LIKE", "%");
        if(session("sort") !== NULL) {
            $query = $query->orderBy('price', session("sort"));
            $sort = session("sort") === "ASC" ? "Price ascending" : "Price descending";
        } else {
            $sort = NULL;
        }
        if(session("filter") !== NULL) {
            $filterArray = explode("-", session("filter"));
            $query = $query->where('price', ">=", $filterArray[0]);
            $query = $query->where('price', "<=", $filterArray[1]);
            $filter = "€" . session("filter");
        } else {
            $filter = NULL;
        }
        if($this->category_id) {
            session(["categoryId" => $this->category_id]);
            $query = $query->whereHas("categories", function($q) {
                $q->where("category_id", "=", $this->category_id);
            });
            $filteredCategory = Category::where("id", "=", $this->category_id)->firstOrFail();
            $filteredCategory = $filteredCategory->title;
        } else {
            session(["categoryId" => NULL]);
            $filteredCategory = NULL;
        }
        $products = $query->paginate(24);
        foreach($products as $product) {
            // get first image
            if(ProductImage::where("product_id", "=", $product->id)->first() !== NULL) {
                $product->image = ProductImage::where("product_id", "=", $product->id)->first()->name;
            } else {
                $product->image = "";
            }
        }

        // return view
        return view("products", compact("shops", "products", "sort", "filter", "filteredCategory", "categories", "header"));
    }

    public function placeFilter(Request $r) {
        // validate request
        $r->validate([
            "filter" => "required",
            "filterType" => "required",
        ]);

        session([$r->filterType => $r->filter]);

        if (auth()->user()->isAdmin && $r->filterType === "shop") {
            return redirect()->route("scrape.index");
        } elseif (session("categoryId") !== NULL) {
            return redirect("/products/filter/" . session("categoryId"));
        } else {
            return redirect()->route("products.index");
        }
    }

    public function destoryFilter(Request $r) {
        // validate request
        $r->validate([
            "filterType" => "required",
        ]);

        session([$r->filterType => NULL]);

        if (auth()->user()->isAdmin && $r->filterType === "shop") {
            return redirect()->route("scrape.index");
        } elseif (session("categoryId") !== NULL) {
            return redirect("/products/filter/" . session("categoryId"));
        } else {
            return redirect()->route("products.index");
        }
    }

    public function detail($product_id) {
        // get shopnames, product and productcategories
        $shops = $this->shops;
        $product = Product::where("id", "=", $product_id)->first();
        $categories = $product->Categories()->get(["title", "category_id"]);
        // get images
        if(ProductImage::where("product_id", "=", $product->id)->first() !== NULL) {
            $productImages = ProductImage::where("product_id", "=", $product->id)->get();
        } else {
            $productImages = "";
        }

        // logged in visitor
        if(!auth()->user() && Visitor::checkPassword()) {
            // set header buttons
            $header["wishlistVisitor"] = true;
            $header["cart"] = true;

            // get product data
            $savedProduct = Savedproduct::where([["wishlist_id", "=", session('wishlist_id')], ["product_id", "=", $product->id]])->first();
            $wishlist = Wishlist::where("id", "=", session('wishlist_id'))->first();
            $product->quantity = $savedProduct->quantity;
            $product->score = $savedProduct->score;
        // admin
        } elseif (auth()->user()->isAdmin) {
            // set header buttons
            $header["arrowLeft"] = true;
            $header["menu"] = true;
            $wishlist = NULL;
        // user
        } elseif (auth()->user()) {
            // set header buttons
            $header["arrowLeft"] = true;
            $header["wishlist"] = true;
            $wishlist = NULL;

            $wishlist = Wishlist::where("user_id", "=", auth()->user()->id)->first();
            // if product already saved to wishlist, display saved data
            if(Savedproduct::where([["wishlist_id", "=", $wishlist->id], ["product_id", "=", $product_id]])->first()) {
                $savedProduct = Savedproduct::where([["wishlist_id", "=", $wishlist->id], ["product_id", "=", $product_id]])->first();
                $product->score = $savedProduct->score;
                $product->quantity = $savedProduct->quantity;
                // check if someone ordered the product
                $product->visitor_id = $savedProduct->visitor_id;
            } else {
                // no one ordered the product
                $product->visitor_id = NULL;
            }
        } else {
            return view("login");
        }

        // return view
        return view("product-detail", compact("shops", "product", "categories", "header", "wishlist", "productImages"));
    }

    public function store(Request $r, $product_id = NULL) {
        // validate request
        $r->validate([
            "score" => "required|integer",
            "quantity" => "required|integer",
        ]);

        $wishlist = Wishlist::where("user_id", "=", auth()->user()->id)->first();

        // check if product already exists
        if(Savedproduct::where([["wishlist_id", "=", $wishlist->id], ["product_id", "=", $product_id]])->first() !== NULL) {
            $savedProduct = Savedproduct::where([["wishlist_id", "=", $wishlist->id], ["product_id", "=", $product_id]])->first();
            $isUpdate = true;
        } else {
            $savedProduct = new Savedproduct();
            $isUpdate = false;
        }

        // add to database
        $savedProduct->score = $r->score;
        $savedProduct->quantity = $r->quantity;
        $savedProduct->product_id = $product_id;
        $savedProduct->wishlist_id = $wishlist->id;

        $savedProduct->save();

        // redirect
        if($isUpdate) {
            return redirect("/wishlist")->with("status", __("Product updated"));
        } else {
            // if there was a filter, return with filter
            if(session("categoryId") !== NULL) {
                return redirect("/products/filter/" . session("categoryId"))->with("status", __("Product added to wishlist"));
            } else {
                return redirect("/products")->with("status", __("Product added to wishlist"));
            }
        }
    }

    public function destroy(Request $r) {
        // delete saved product
        Savedproduct::find($r->product_id)->delete();

        return redirect()->back()->with("status-warning", __("Product deleted"));
    }
}
