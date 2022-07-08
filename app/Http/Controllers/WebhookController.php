<?php

namespace App\Http\Controllers;

use App\Mail\sendNewOrderedMail;
use App\Mail\sendNewReservedMail;
use App\Models\Product;
use App\Models\Savedproduct;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mollie\Laravel\Facades\Mollie;

class WebhookController extends Controller
{
    //
    public function handle(Request $r) {
        if (! $r->has('id')) {
            return;
        }

        $payment = Mollie::api()->payments()->get($r->id);

        if ($payment->isPaid() && ! $payment->hasRefunds() && ! $payment->hasChargebacks()) {

            $visitorId = $payment->metadata->order_id;
            $visitor = Visitor::findOrFail($visitorId);
            $visitor->orderStatus = "paid";
            $visitor->save();

            foreach($payment->metadata->cart as $item) {
                $savedProdudct = Savedproduct::where("product_id", "=", $item->id)->first();
                $savedProdudct->visitor_id = $visitorId;
                $savedProdudct->save();
            }

            $savedProducts = Savedproduct::where("visitor_id", "=", $visitor->id)->get();
            $wishlist = "";
            foreach($savedProducts as $savedProduct) {
                $product = Product::where("id", "=", $savedProduct->product_id)->firstOrFail();
                $savedProduct->price = $product->price;
                $savedProduct->title = $product->title;
                $wishlist = Wishlist::where("id", "=", $savedProduct->wishlist_id)->firstOrFail();
            }
            $user = User::where("id", "=", $wishlist->user_id)->firstOrFail();

            Mail::to($visitor->email)->send(new sendNewOrderedMail($visitor, $wishlist, $savedProducts));
            Mail::to($user->email)->send(new sendNewReservedMail($visitor, $user, $savedProducts, $wishlist));

            Log::alert("Betaling is gebeurt");

        } elseif ($payment->isOpen()) {
            /*
             * The payment is open.
             */
        } elseif ($payment->isPending()) {
            /*
             * The payment is pending.
             */
        } elseif ($payment->isFailed()) {
            /*
             * The payment has failed.
             */
        } elseif ($payment->isExpired()) {
            /*
             * The payment is expired.
             */
        } elseif ($payment->isCanceled()) {
            /*
             * The payment has been canceled.
             */
        } elseif ($payment->hasRefunds()) {
            /*
             * The payment has been (partially) refunded.
             * The status of the payment is still "paid"
             */
        } elseif ($payment->hasChargebacks()) {
            /*
             * The payment has been (partially) charged back.
             * The status of the payment is still "paid"
             */
        }
    }
}
