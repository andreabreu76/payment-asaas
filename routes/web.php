<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

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

// Payment Routes
Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
Route::post('/payments/process', [PaymentController::class, 'process'])->name('payments.process');
Route::get('/payments/thank-you', [PaymentController::class, 'thankYou'])->name('payments.thank-you');

// Redirect root to payments page
Route::get('/', function () {
    return redirect()->route('payments.index');
});
