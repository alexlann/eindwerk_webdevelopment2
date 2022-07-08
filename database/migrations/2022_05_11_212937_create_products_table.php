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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id');
            $table->string('title');
            $table->string('scrape_url');
            $table->mediumText('detail_url');
            $table->string('tags')
                   ->nullable()
                   ->default(NULL);
            $table->string('age')
                  ->nullable()
                  ->default(NULL);
            $table->string('store');
            $table->mediumText('description')
                  ->nullable()
                  ->default(NULL);
            $table->float('price');
            $table->timestamp('scraped_on')->useCurrent();
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
        Schema::dropIfExists('products');
    }
};
