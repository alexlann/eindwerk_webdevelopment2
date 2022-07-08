<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\Admin\ScrapeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [Controller::class, "redirect"]);

// ScrapeController
Route::get('/scrape', [ScrapeController::class, "index"])
    ->middleware(['auth'])->name('scrape.index');
Route::post('/scrape/categories', [ScrapeController::class, "scrapeCategories"])
    ->middleware(['auth'])->name('scrape.categories');
Route::post('/scrape/products', [ScrapeController::class, "scrapeProducts"])
    ->middleware(['auth'])->name('scrape.products');
Route::get('/scrape/prices', [ScrapeController::class, "prices"])
    ->middleware(['auth'])->name('scrape.pricesOverview');
Route::post('/scrape/prices', [ScrapeController::class, "scrapePrices"])
    ->middleware(['auth'])->name('scrape.prices');

// ProductController
Route::get('/products', [ProductController::class, "index"])
    ->middleware(['auth'])->name('products.index');
Route::get('/products/filter/{category_id}', [ProductController::class, "index"])
    ->middleware(['auth'])->name('products.filter');
Route::post('products/filter/place', [ProductController::class, "placeFilter"])
    ->middleware(['auth'])->name('products.placeFilter');
Route::post('products/filter/delete', [ProductController::class, "destoryFilter"])
    ->middleware(['auth'])->name('products.deleteFilter');
Route::get('/products/{product_id}', [ProductController::class, "detail"])
    ->name('products.detail');
Route::post('/products/{product_id}/add', [ProductController::class, "store"])
    ->middleware(['auth'])->name('product.store');
Route::delete('/wishlist/delete', [ProductController::class, "destroy"])
    ->middleware(['auth'])->name('savedItem.delete');

// VisitorController
Route::get('/wishlist/{wishlist_slug}/login', [VisitorController::class, "login"])
    ->name('visitor.login');
Route::post('/wishlist/login', [VisitorController::class, "storeLogin"])
    ->name('visitor.storeLogin');
Route::get('/address', [VisitorController::class, "address"])
    ->name('visitor.address');
Route::post('/address', [VisitorController::class, "storeAddress"])
    ->name('visitor.storeAddress');

// CartController
Route::get('/cart', [CartController::class, "index"])
    ->name('cart.index');
Route::post('/cart/add', [CartController::class, "store"])
    ->name('cart.store');
Route::delete('/cart/delete', [CartController::class, "destroy"])
    ->name('cart.delete');

// CheckoutController
Route::post('/checkout', [CheckoutController::class, "checkCart"])
    ->name('checkout');
Route::get('/checkout/succes', [CheckoutController::class, "success"])
    ->name('checkout.success');

// WebhooksController
Route::get('/webhooks/mollie', [WebhookController::class, "handle"])
    ->name('webhooks.mollie');

// WishlistController
Route::get('/wishlist/close', [WishlistController::class, "close"])
    ->middleware(['auth'])->name('wishlist.close');
Route::get('/wishlist/edit', [WishlistController::class, "edit"])
    ->middleware(['auth'])->name('wishlist.edit');
Route::post('/wishlist/edit', [WishlistController::class, "store"])
    ->middleware(['auth'])->name('wishlist.store');
Route::get('/wishlist', [WishlistController::class, "index"])
    ->middleware(['auth'])->name('wishlist.index');
Route::get('/wishlist/{wishlist_slug}', [WishlistController::class, "index"])
    ->name('visitor.index');

require __DIR__.'/auth.php';
