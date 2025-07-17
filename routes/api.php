<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ChoziCodeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes (no authentication required)
Route::post('chozi-codes/validate', [ChoziCodeController::class, 'validateCode']);

// Protected routes (authentication required)
Route::middleware(['auth:sanctum'])->group(function () {
    
    // User profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Payment Routes
    Route::prefix('payments')->group(function () {
        Route::post('/', [PaymentController::class, 'processPayment']);
        Route::get('/', [PaymentController::class, 'getPaymentHistory']);
        Route::get('/stats', [PaymentController::class, 'getPaymentStats']);
        Route::get('/{paymentReference}', [PaymentController::class, 'getPaymentDetails']);
    });
    
    // ChoziCode Routes
    Route::prefix('chozi-codes')->group(function () {
        Route::post('/', [ChoziCodeController::class, 'generateCode']);
        Route::get('/', [ChoziCodeController::class, 'getBrokerCodes']);
        Route::get('/analytics', [ChoziCodeController::class, 'getCodeAnalytics']);
        Route::delete('/{codeId}', [ChoziCodeController::class, 'deactivateCode']);
    });
    
    // User Search for Payments
    Route::get('users/search', [ChoziCodeController::class, 'searchUsers']);
    
});

// API Documentation routes (for development)
Route::get('docs', function () {
    return response()->json([
        'api_name' => 'ChoziPay API',
        'version' => '1.0.0',
        'endpoints' => [
            'Authentication' => [
                'POST /api/auth/login' => 'Login user',
                'POST /api/auth/logout' => 'Logout user',
                'POST /api/auth/register' => 'Register new user',
            ],
            'Payments' => [
                'POST /api/payments' => 'Process payment',
                'GET /api/payments' => 'Get payment history',
                'GET /api/payments/stats' => 'Get payment statistics',
                'GET /api/payments/{reference}' => 'Get payment details',
            ],
            'ChoziCodes' => [
                'POST /api/chozi-codes' => 'Generate ChoziCode (brokers only)',
                'GET /api/chozi-codes' => 'Get broker ChoziCodes',
                'GET /api/chozi-codes/analytics' => 'Get ChoziCode analytics',
                'POST /api/chozi-codes/validate' => 'Validate ChoziCode (public)',
                'DELETE /api/chozi-codes/{id}' => 'Deactivate ChoziCode',
            ],
            'Users' => [
                'GET /api/user' => 'Get current user',
                'GET /api/users/search' => 'Search users for payments',
            ],
        ],
        'authentication' => 'Bearer token (Laravel Sanctum)',
        'rate_limiting' => 'Applied to sensitive endpoints',
    ]);
});
