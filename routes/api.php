<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\OrderApiController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/register', [AuthApiController::class, 'register']);

/*
|--------------------------------------------------------------------------
| USERS (JANGAN DIHAPUS ✅)
|--------------------------------------------------------------------------
*/
Route::get('/users', [AuthApiController::class, 'users']);

/*
|--------------------------------------------------------------------------
| ORDERS - UMUM / USER
|--------------------------------------------------------------------------
*/
Route::post('/orders', [OrderApiController::class, 'store']);
Route::get('/orders', [OrderApiController::class, 'index']);
Route::get('/orders/{id}', [OrderApiController::class, 'show']);
Route::get('/my-orders/{userId}', [OrderApiController::class, 'myOrders']);

/*
|--------------------------------------------------------------------------
| ADMIN / DRIVER
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // update SATU order
    Route::put(
        '/admin/orders/{id}/status',
        [OrderApiController::class, 'updateStatus']
    );

    // 🔥 TAMBAHAN: update BANYAK order
    Route::put(
        '/admin/orders/bulk-status',
        [OrderApiController::class, 'bulkUpdateStatus']
    );

    // 🔥 TAMBAHAN: update SEMUA order
    Route::put(
        '/admin/orders/status-all',
        [OrderApiController::class, 'updateAllStatus']
    );

    // get current user (cukup SATU)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
