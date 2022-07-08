<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savedproducts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained();
            $table->foreignId('wishlist_id')
                  ->constrained();
            $table->foreignId('visitor_id')
                  ->nullable()
                  ->unsigned()
                  ->default(NULL);
            $table->integer('score');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('savedproducts');
    }
};
