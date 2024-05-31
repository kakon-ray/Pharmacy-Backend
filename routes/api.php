<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientDashboard;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/reset_password', [PasswordResetRequestController::class, 'reset_password_submit'])->name('reset_password');
Route::get('reset/password/{token}', [PasswordResetRequestController::class, 'show_reset_password_form'])->name('reset.password');
Route::post('/new-password', [PasswordResetRequestController::class, 'new_password_submit'])->name('new.password.submit');


// login and registration and check
Route::post('/sign_up', [RegController::class, 'regisign_upster'])->name('sign_up');
Route::post('/user_login', [ClientDashboard::class, 'login'])->name('user_login');
Route::get('/product', [ProductController::class, 'product'])->name('product');

// verification email
Route::post('/email-verified', [RegController::class, 'email_verified'])->name('email_verified');


Route::group(['middleware' => ['jwt.role:userbasic', 'jwt.auth']], function ($router) {
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/product/add', [ProductController::class, 'product_add'])->name('product_add');
    Route::post('/product/edit', [ProductController::class, 'product_edit'])->name('product_edit');
    Route::post('/product/delete', [ProductController::class, 'product_delete'])->name('product_delete');
});


// google and facebook login
Route::get('auth/{provider}', [SocialiteController::class, 'loginSocial'])
    ->middleware(['web']);

Route::get('auth/{provider}/callback', [SocialiteController::class, 'callbackSocial'])
    ->middleware(['web']);



    // php artisan serve --host 192.168.5.239 --port 8000    
    // 192.168.5.239 my ip
    // ipconfig (See My IP)