<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProductControllerController;
use App\Models\Category;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);


Route::post('/product/restore/{id}', [ProductController::class, 'restore']);
Route::get('/products', [ProductController::class, 'index']);
Route::resource('/product', ProductController::class);


Route::post('/category/restore/{id}', [CategoryController::class, 'restore']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::resource('/category', CategoryController::class);


Route::post('/brand/restore/{id}', [BrandController::class, 'restore']);
Route::get('/brands', [BrandController::class, 'index']);
Route::resource('/brand', BrandController::class);