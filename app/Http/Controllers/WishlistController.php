<?php

namespace App\Http\Controllers;

use App\Mail\sendNewCloseWishlistAdminMail;
use App\Mail\sendNewCloseWishlistUserMail;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Savedproduct;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WishlistController extends Controller
{
    //
    public function index($wishlist_slug = NULL) {
        // if user
        if(auth()->user() !== NULL && !auth()->user()->isAdmin) {
            if(!Wishlist::where("user_id", "=", auth()->user()->id)->first()) {
                return redirect("/wishlist/edit");
            }
            $wishlist = Wishlist::where("user_id", "=", auth()->user()->id)->first();
            $productInformation = $this->getAllProductInformation($wishlist->id);

            $header["search"] = true;
            $header["logout"] = true;
        // if logged in visitor
        } elseif(Wishlist::where("slug", "=", $wishlist_slug)->first() != NULL && Visitor::checkPassword()) {
            $wishlist = Wishlist::where("slug", "=", $wishlist_slug)->first();
            $productInformation = $this->getAllProductInformation($wishlist->id);
            $header["cart"] = true;
        // if visitor not logged in
        } elseif (Wishlist::where("slug", "=", $wishlist_slug)->first() != NULL) {
            return redirect("wishlist/" . $wishlist_slug . "/login");
        // if admin
        } elseif (auth()->user() !== NULL && auth()->user()->isAdmin) {
            return redirect("/products");
        // if not logged in
        } else {
            return redirect("/login");
        }

        // return view
        return view("wishlist", compact("wishlist", "header"), [
            "totalQuantityCount" => $productInformation[0],
            "totalPrice" => $productInformation[1],
            "orderedQuantityCount" => $productInformation[2],
            "notOrderedCount" => $productInformation[3],
            "orderedPrice" => $productInformation[4],
            "savedProducts" => $productInformation[5],
        ]);
    }

    private function getAllProductInformation($wishlist_id) {
        $totalQuantityCount = 0;
        $totalPrice = 0;
        $orderedQuantityCount = 0;
        $orderedPrice = 0;
        $notOrderedCount = 0;
        $savedProducts = Savedproduct::where("wishlist_id", "=", $wishlist_id)->orderBy("score", "DESC")->get();
        if($savedProducts) {
            foreach($savedProducts as $savedProduct) {
                $product = Product::where("id", "=", $savedProduct->product_id)->first();
                $savedProduct->title = $product->title;
                $savedProduct->store = $product->store;
                $savedProduct->price = $product->price;
                // get first image
                $savedProduct->image = ProductImage::where("product_id", "=", $product->id)->firstOrFail()->name;
                // for all products
                $totalQuantityCount = $totalQuantityCount + $savedProduct->quantity;
                $totalPrice = $totalPrice + ($savedProduct->price * $savedProduct->quantity);
                // for ordered products
                $orderedQuantityCount = $savedProduct->visitor_id ? $orderedQuantityCount + $savedProduct->quantity : $orderedQuantityCount;
                $orderedPrice = $savedProduct->visitor_id ? $orderedPrice + ($savedProduct->price * $savedProduct->quantity) : $orderedPrice;
                $notOrderedCount = !$savedProduct->visitor_id ? $notOrderedCount + 1 : $notOrderedCount;
                // save name of person who ordered product
                if($savedProduct->visitor_id !== NULL) {
                    $visitor = Visitor::where("id", "=", $savedProduct->visitor_id)->firstOrFail();
                    $savedProduct->orderName = $visitor->first_name . " " . $visitor->last_name;
                }
            }
        }
        return [$totalQuantityCount, $totalPrice, $orderedQuantityCount, $notOrderedCount, $orderedPrice, $savedProducts];
    }

    public function edit() {
        if(auth()->user()->isAdmin) {
            return redirect("/products");
        }
        $wishlist = Wishlist::where("user_id", "=", auth()->user()->id)->first();
        $header['arrowLeft'] = true;

        // return view
        return view("wishlist-edit", compact("header"), ["wishlist" => $wishlist]);
    }

    public function store(Request $r) {
        // slugify request
        $r->slug = Wishlist::createSlug($r->slug);

                // check if wishlist already exists
        if(Wishlist::where("user_id", "=", auth()->user()->id)->first() !== NULL) {
            // validate request
            $r->validate([
                "name" => "required|max:255",
                "description" => "required|max:255",
                "slug" => "required|max:255",
                "password" => "required|max:255",
                'image' => 'file|required|max:1000|mimes:png,jpg,gif,bmp'
            ]);

            $wishlist = Wishlist::where("user_id", "=", auth()->user()->id)->first();
        } else {
            // validate request
            $r->validate([
                "name" => "required|max:255",
                "description" => "required|max:255",
                "slug" => "required|max:255|unique:wishlists,slug",
                "password" => "required|max:255",
                'image' => 'file|required|max:1000|mimes:png,jpg,gif,bmp'
            ]);
            $wishlist = new Wishlist();
        }

        // get extension
        $ext = $r->image->getClientOriginalExtension();

        // make random file name, with day-prefix
        $randomName = date('d') . '-' . Str::random(10) . '.' . $ext;

        // path magic
        $filePath = 'uploads/' . date('Y/m/');
        $fullPath = $filePath . $randomName;

        // upload files in symbolic public folder (make accessable)
        /** @var Illuminate\Filesystem\FilesystemAdapter */
        $fileSystem = Storage::disk('public');
        $fileSystem->putFileAs($filePath, $r->image, $randomName);

        // add to database
        $wishlist->name = $r->name;
        $wishlist->description = $r->description;
        $wishlist->slug = Wishlist::createSlug($r->slug);
        $wishlist->image = $fullPath;
        $wishlist->password = Hash::make($r->password);
        $wishlist->user_id = auth()->user()->id;

        $wishlist->save();

        return redirect("/wishlist")->with("status", __("Wishlist edited"));
    }

    public function close() {
        // get admin
        $admin = User::where("isAdmin", "=", 1)->firstOrFail();
        // get user
        $user = User::where("id", "=", auth()->user()->id)->firstOrFail();
        // get wishlist
        $wishlist = Wishlist::where("user_id", "=", $user->id)->firstOrFail();

        $wishlist->isClosed = TRUE;
        $wishlist->save();

        // send mail to admin and user
        Mail::to($admin->email)->send(new sendNewCloseWishlistAdminMail($user, $wishlist));
        Mail::to($user->email)->send(new sendNewCloseWishlistUserMail($user, $wishlist));

        return redirect()->back()->with("status-warning", __("Wishlist closed"));
    }
}
