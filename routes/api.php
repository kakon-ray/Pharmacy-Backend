<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\RegController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\UserManageController;
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
    Route::get('/me/{token}', [AuthController::class, 'me'])->name('me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');

    // manage category
    Route::get('/category', [CategoryController::class, 'category'])->name('category');
    Route::get('/category/getitem/{id}', [CategoryController::class, 'category_get_item'])->name('category_get_item');
    Route::post('/category/add', [CategoryController::class, 'category_add'])->name('category_add');
    Route::post('/category/edit', [CategoryController::class, 'category_edit'])->name('category_edit');
    Route::get('/category/delete/{id}', [CategoryController::class, 'category_delete'])->name('category_delete');
   
    // manage company
    Route::get('/company', [CompanyController::class, 'company'])->name('company');
    Route::get('/company/getitem/{id}', [CompanyController::class, 'company_get_item'])->name('company_get_item');
    Route::post('/company/add', [CompanyController::class, 'company_add'])->name('company_add');
    Route::post('/company/edit', [CompanyController::class, 'company_edit'])->name('company_edit');
    Route::get('/company/delete/{id}', [CompanyController::class, 'company_delete'])->name('company_delete');


    // manage medicine

    Route::get('/medicine', [MedicineController::class, 'medicine'])->name('medicine');
    Route::get('/medicine/getitem/{id}', [MedicineController::class, 'medicine_get_item'])->name('medicine_get_item');
    Route::post('/medicine/add', [MedicineController::class, 'medicine_add'])->name('medicine_add');
    Route::post('/medicine/edit', [MedicineController::class, 'medicine_edit'])->name('medicine_edit');
    Route::get('/medicine/delete/{id}', [MedicineController::class, 'medicine_delete'])->name('medicine_delete');
    Route::get('/get/company/category', [MedicineController::class, 'get_company_category'])->name('get_company_category');


    Route::get('/userinfo', [UserManageController::class, 'get_user']);
    Route::get('/userpermission/{id}', [UserManageController::class, 'userpermission']);
    Route::get('/canclepermission/{id}', [UserManageController::class, 'canclepermission']);
});


// google and facebook login
Route::get('auth/{provider}', [SocialiteController::class, 'loginSocial'])
    ->middleware(['web']);

Route::get('auth/{provider}/callback', [SocialiteController::class, 'callbackSocial'])
    ->middleware(['web']);



    // php artisan serve --host 192.168.5.239 --port 8000    
    // 192.168.5.239 my ip
    // ipconfig (See My IP)