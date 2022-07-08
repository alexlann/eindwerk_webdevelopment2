<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class sendNewCloseWishlistUserMail extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $wishlist;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Wishlist $wishlist)
    {
        $this->user = $user;
        $this->wishlist = $wishlist;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('Your wishlist will be closed'))
        ->markdown('emails.wishlist.user', [
            'user' => $this->user,
            'wishlist' => $this->wishlist
        ]);
    }
}
