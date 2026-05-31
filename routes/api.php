<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('products', ProductController::class);


Route::post('orders', [OrderController::class, 'store']);
Route::get('orders/{id}', [OrderController::class, 'show']);
