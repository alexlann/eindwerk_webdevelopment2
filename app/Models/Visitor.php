<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Visitor extends Model
{
    use HasFactory;

    public static function checkPassword() {
        if(session("wishlist_id") !== NULL && session("password") !== NULL) {
            $wishlist = Wishlist::where("id", "=", session("wishlist_id"))->first();

            return Hash::check(session("password"), $wishlist->password);
        } else {
            return false;
        }
    }
}
