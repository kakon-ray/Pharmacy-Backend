<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\RegController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/reset_password', [PasswordResetRequestController::class, 'reset_password_submit'])->name('reset_password');
Route::get('reset/password/{token}', [PasswordResetRequestController::class, 'show_reset_password_form'])->name('reset.password');
Route::post('/new-password', [PasswordResetRequestController::class, 'new_password_submit'])->name('new.password.submit');


// login and registration and check
Route::post('/sign_up', [RegController::class, 'regisign_upster'])->name('sign_up');
Route::post('/user_login', [LoginController::class, 'login'])->name('user_login');


// verification email
Route::post('/email-verified', [RegController::class, 'email_verified'])->name('email_verified');


Route::group(['middleware' => ['jwt.role:userbasic', 'jwt.auth']], function ($router) {
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');


    Route::get('/medicine', [MedicineController::class, 'medicine'])->name('medicine');
    Route::get('/medicine/getitem/{id}', [MedicineController::class, 'medicine_get_item'])->name('medicine_get_item');
    Route::post('/medicine/add', [MedicineController::class, 'medicine_add'])->name('medicine_add');
    Route::post('/medicine/edit', [MedicineController::class, 'medicine_edit'])->name('medicine_edit');
    Route::get('/medicine/delete/{id}', [MedicineController::class, 'medicine_delete'])->name('medicine_delete');


});


// google and facebook login
Route::get('auth/{provider}', [SocialiteController::class, 'loginSocial'])
    ->middleware(['web']);

Route::get('auth/{provider}/callback', [SocialiteController::class, 'callbackSocial'])
    ->middleware(['web']);



    // php artisan serve --host 192.168.5.239 --port 8000    
    // 192.168.5.239 my ip
    // ipconfig (See My IP)