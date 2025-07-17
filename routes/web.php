<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminController;

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

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Custom Authentication Routes (overriding Laravel UI)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes (authenticated users only)
Route::middleware(['auth', 'security'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
    
    // Payment interface routes
    Route::get('/payments', function () {
        return view('payments.index');
    })->name('payments.index');
    
    Route::get('/payments/send', function () {
        return view('payments.send');
    })->name('payments.send');
    
    Route::get('/payments/{reference}', function ($reference) {
        return view('payments.details', compact('reference'));
    })->name('payments.details');
    
    // ChoziCode management routes (broker only)
    Route::middleware(['role:broker'])->group(function () {
        Route::get('/chozi-codes', function () {
            return view('chozi-codes.index');
        })->name('chozi-codes.index');
        
        Route::get('/chozi-codes/analytics', function () {
            return view('chozi-codes.analytics');
        })->name('chozi-codes.analytics');
    });
    
    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::patch('/admin/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    });
    
    // Profile management
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile.index');
    
    Route::get('/transactions', function () {
        return view('transactions.index');
    })->name('transactions.index');
});

// Fallback for Laravel UI (if needed)
Auth::routes(['register' => false, 'login' => false]);

// Legacy home route redirect
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->name('home');
