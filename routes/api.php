<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

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

// Place a new order

// Get all orders for the authenticated user
// Route::get('/orders', [OrdersController::class, 'orders'])->middleware('auth:api');
Route::get('/orders', [OrdersController::class, 'orders']);
Route::get('/orders/{id}', [OrdersController::class, 'orders'])->middleware('auth:api');
Route::post('/orders', [OrdersController::class, 'placeOrder']);


Route::post('/cart', [CartController::class, 'addToCart']);
Route::get('/cart', [CartController::class, 'viewCart']);
Route::put('/cart', [CartController::class, 'updateCart']);
Route::delete('/cart', [CartController::class, 'removeFromCart']);
