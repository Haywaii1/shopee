<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/home', [HomeController::class, 'home']);
Route::get('/products', [ProductController::class, 'products'])->name('products');
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/store-product', [ProductController::class, 'storeProduct'])->name('store.product');
Route::post('/update-product/{id}', [ProductController::class, 'updateProduct'])->name('update.product');
Route::post('/delete/{id}', [ProductController::class, 'delete'])->name('delete');

Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::post('/password-email', [UserController::class, 'passwordEmail'])->name('password.email');
Route::post('/password-update', [UserController::class, 'passwordUpdate'])->name('password.update');

// Place a new order

// Get all orders for the authenticated user
// Route::get('/orders', [OrdersController::class, 'orders'])->middleware('auth:api');
Route::get('/orders', [OrdersController::class, 'orders']);
Route::get('/orders/{id}', [OrdersController::class, 'orders']);
Route::post('/orders', [OrdersController::class, 'placeOrder']);
Route::delete('/orders/{id}', [OrdersController::class, 'deleteOrder']);

Route::post('/cart', [CartController::class, 'addToCart']);
Route::get('/cart', [CartController::class, 'viewCart']);
Route::put('/cart', [CartController::class, 'updateCart']);
Route::delete('/cart', [CartController::class, 'removeFromCart']);

Route::get('/email/verify/{id}', [UserController::class, 'verify'])->name('verification.verify');
Route::get('/email/resend', [UserController::class, 'resend'])->name('verification.resend');
Route::get('/password-reset', [UserController::class, 'passwordReset'])->name('password.reset')->middleware('guest');


// Customer care contact

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/feedback', [FeedbackController::class, 'store']);
    Route::get('/feedback', [FeedbackController::class, 'index']);
    Route::get('/feedback/{id}', [FeedbackController::class, 'show']);
    Route::delete('/feedback/{id}', [FeedbackController::class, 'destroy']);
});

