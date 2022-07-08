<?php

namespace App\Mail;

use App\Models\Savedproduct;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Wishlist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class sendNewReservedMail extends Mailable
{
    use Queueable, SerializesModels;

    private $visitor;
    private $user;
    private $wishlist;
    private $savedProducts;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Visitor $visitor, User $user, $savedProducts, Wishlist $wishlist)
    {
        $this->visitor = $visitor;
        $this->user = $user;
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
        return $this->subject($this->visitor->first_name . " " . $this->visitor->last_name . " " . __('reserved items from your wishlist'))
        ->markdown('emails.order.user', [
            'visitor' => $this->visitor,
            'user' => $this->user,
            'wishlist' => $this->wishlist,
            'savedProducts' => $this->savedProducts,
        ]);
    }
}
