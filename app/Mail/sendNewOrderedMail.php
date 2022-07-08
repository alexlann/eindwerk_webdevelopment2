<?php

namespace App\Mail;

use App\Models\Savedproduct;
use App\Models\Visitor;
use App\Models\Wishlist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class sendNewOrderedMail extends Mailable
{
    use Queueable, SerializesModels;

    private $visitor;
    private $wishlist;
    private $savedProducts;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Visitor $visitor, Wishlist $wishlist, $savedProducts)
    {
        $this->visitor = $visitor;
        $this->wishlist = $wishlist;
        $this->savedProducts = $savedProducts;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('Your order is confirmed'))
        ->markdown('emails.order.visitor', [
            'visitor' => $this->visitor,
            'wishlist' => $this->wishlist,
            'savedProducts' => $this->savedProducts,
        ]);
    }
}
