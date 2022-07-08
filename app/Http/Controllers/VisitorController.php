<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class VisitorController extends Controller
{
    //
    public function login($wishlist_slug) {
        // check if visitor already loged in
        if(Visitor::checkPassword()) {
            // get wishlist

            $wishlist = Wishlist::where("id", "=", session("wishlist_id"))->firstOrFail();

            // return to correct wishlist
            return redirect()->route("visitor.index", $wishlist->slug);
        } else {
            // get wishlist from slug and store in session
            $wishlist = Wishlist::where("slug", "=", $wishlist_slug)->firstOrFail();
            session(["wishlist_id" => $wishlist->id]);

            // return view
            return view("visitor-login", compact("wishlist"));
        }
    }

    public function storeLogin(Request $r) {
        // Validate password
        $r->validate([
            "password" => "required|max:255"
        ]);

        // store hashed password in session
        session(['password' => $r->password]);

        if(Visitor::checkPassword()) {
            session(["wrongVisitorPassword" => FALSE]);
            // get wishlist
            $wishlist = Wishlist::where("id", "=", session("wishlist_id"))->firstOrFail();

            // return to correct wishlist
            return redirect()->route("visitor.index", $wishlist->slug);
        } else {
            session(["wrongVisitorPassword" => TRUE]);
            return redirect()->back();
        }
    }

    public function address() {
        if(Visitor::checkPassword()) {
            $header["arrowCart"] = true;

            if(session("visitor_id") !== NULL) {
                $visitor = Visitor::where("id", "=", session("visitor_id"))->first();
            } else {
                $visitor = NULL;
            }

            // return view
            return view("address", compact("header", "visitor"));
        } else {
            return redirect("/login");
        }
    }

    public function storeAddress(Request $r) {
        if(Visitor::checkPassword()) {
            // validate request
            $r->validate([
                "firstname" => "required|max:255",
                "lastname" => "required|max:255",
                "email" => "required|max:255|email",
                "street" => "required|max:255",
                "city" => "required|max:255",
                "zipcode" => "required|max:255",
            ]);

            if(session("visitor_id") !== NULL) {
                $visitor = Visitor::where("id", "=", session("visitor_id"))->firstOrFail();
                $edit = true;
            } else {
                $visitor = new Visitor();
                $edit = false;
            }

            // add to database
            $visitor->first_name = $r->firstname;
            $visitor->last_name = $r->lastname;
            $visitor->email = $r->email;
            $visitor->street = $r->street;
            $visitor->city = $r->city;
            $visitor->zipcode = $r->zipcode;

            $visitor->save();

            // add visitor id to cart
            foreach(Cart::session(1)->getContent() as $product) {
                Cart::session(1)->update($product->id, [
                    'attributes' => array(
                        'score' => $product->attributes['score'],
                        'store' => $product->attributes['store'],
                        'image' => $product->attributes['image'],
                        'visitorId' => $visitor->id
                    )
                ]);
            }

            session(["visitor_id" => $visitor->id]);

            if($edit) {
                return redirect()->route("cart.index")->with("status", __("Address edited"));
            } else {
                return redirect()->route("cart.index")->with("status", __("Address added"));
            }
        } else {
            return redirect()->route("login");
        }
    }

}
