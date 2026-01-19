<?php

use App\Http\Controllers\Api\TestOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/orders', [TestOrderController::class, 'index']);
Route::get('/orders/{id}', [TestOrderController::class, 'show']);
Route::patch('/orders/{id}/status', [TestOrderController::class, 'updateStatus']);
Route::post('/orders/{id}/cancel-request', [TestOrderController::class, 'requestCancel']);
Route::post('/orders/{id}/handle-cancel', [TestOrderController::class, 'handleCancelRequest']);
Route::patch('/return-requests/{id}', [TestOrderController::class, 'updateReturnStatus']);
