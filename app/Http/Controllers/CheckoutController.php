<?php

namespace App\Http\Controllers;

use App\Models\Savedproduct;
use App\Models\Visitor;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Mollie\Laravel\Facades\Mollie;

class CheckoutController extends Controller
{
    //
    public function checkCart(Request $r) {
        // check if everything is filled in and save
        if(Visitor::checkPassword()) {
            // validate request
            $r->validate([
                "message" => "required|max:511",
            ]);

            // check if cart is filled
            if(Cart::session(1)->getContent()->count() > 0) {
                // check if personal information is filled in
                if(session("visitor_id") &&Visitor::where("id", "=", session("visitor_id"))->firstOrFail()) {
                    // add to database
                    $visitor = Visitor::where("id", "=", session("visitor_id"))->firstOrFail();
                    $visitor->message = $r->message;
                    $visitor->orderStatus = "pending";
                    $visitor->save();

                    $this->checkout($visitor->id);
                } else {
                    return redirect()->back()->with("status-warning", __("Personal information is empty"));
                }
            } else {
                return redirect()->back()->with("status-warning", __("Your pram is empty"));
            }

        } else {
            return redirect()->route("login");
        }
    }


    private function checkout($visitorId) {
        // webhook logic, make accessable from online enviroment
        $webhookUrl = route('webhooks.mollie');

        if(App::environment('local')) {
            $webhookUrl = "https://8932-193-121-108-198.eu.ngrok.io/webhooks/mollie";
        }

        // log some info
        Log::alert("Before Mollie Checkout, toal price is calculated.");

        // create payment
        $cart = Cart::session(1);
        $total = number_format((string) $cart->getTotal(), 2);
        $visitor = Visitor::where("id", "=", $visitorId)->firstOrFail();

        $payment = Mollie::api()->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => $total // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            "description" => __("Order on ") . date("d-m-Y h:i"),
            "redirectUrl" => route('checkout.success'),
            "webhookUrl" => $webhookUrl,
            "metadata" => [
                "order_id" => $visitorId,
                "order_name" => $visitor->first_name . " " . $visitor->last_name,
                "cart" => $cart->getContent(),
            ],
        ]);

        // redirect customer to Mollie checkout page
        return redirect($payment->getCheckoutUrl(), 303);
    }

    public function success() {
        return view("success");
    }

}
