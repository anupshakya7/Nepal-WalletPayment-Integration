<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/',[HomeController::class,'index'])->name('home');

Route::resource('product',ProductController::class);

Route::get('/esewa-pay',[OrderController::class,'esewaOrder'])->name('esewa.form');
Route::post('/esewa-pay',[OrderController::class,'esewaPay'])->name('esewa.pay');
// Route::get('/redirect',[OrderController::class,'redirectPage'])->name('esewa.redirect');


// Route::get('/status',[OrderController::class,'status'])->name('esewa.status');
Route::get('/esewa-success',[OrderController::class,'esewaSuccess'])->name('esewa.success');
Route::get('/esewa-fail',[OrderController::class,'esewaFail'])->name('esewa.fail');


