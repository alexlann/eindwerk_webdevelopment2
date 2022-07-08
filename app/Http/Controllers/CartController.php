<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Savedproduct;
use App\Models\Visitor;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class CartController extends Controller
{
    //
    public function index() {
        // check password
        if(Visitor::checkPassword()) {
            // get current wishlist
            $wishlist = Wishlist::where("id", "=", session("wishlist_id"))->firstOrFail();

            // if visitor has filled in address, get visitor
            if(session("visitor_id") !== NULL) {
                $visitor = Visitor::where("id", "=", session("visitor_id"))->firstOrFail();
            } else {
                $visitor = NULL;
            }

            // set header button
            $header["wishlistVisitor"] = true;

            $cart = Cart::session(1);

            // return view
            return view("cart", compact("header", "wishlist", "cart", "visitor"));
        } else {
            return redirect()->route("login");
        }
    }

    public function store(Request $r) {
        // check password
        if(Visitor::checkPassword()) {
            $product = Product::findOrFail($r->product_id);
            $productImage = ProductImage::where("product_id", "=", $r->product_id)->first();
            $savedProduct = Savedproduct::where([["product_id", "=", $r->product_id], ["wishlist_id", "=", session("wishlist_id")]])->firstOrFail();
            // add the product to cart
            Cart::session(1)->add(array(
                'id' => $product->id,
                'name' => $product->title,
                'price' => $product->price,
                'quantity' => $savedProduct->quantity,
                'attributes' => array(
                    'score' => $savedProduct->score,
                    'store' => $product->store,
                    'image' => $productImage->name
                ),
                'associatedModel' => $savedProduct
            ));

            // get current wishlist
            return redirect()->back()->with("status", __("Added to pram"));
        } else {
            return redirect()->route("login");
        }
    }

    public function destroy(Request $r) {
        // delete saved cart item
        Cart::session(1)->remove($r->product_id);

        return redirect()->back()->with("status-warning", __("Removed from pram"));
    }
}
