<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoardingCardController;
use App\Services\BoardingCardService;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**
 * Boarding cards sorting.
 */
Route::get('/boarding-cards/sort', [BoardingCardController::class, 'sort']);

/**
 * Boarding cards CRUD operations.
 */
Route::get('/boarding-cards', [BoardingCardController::class, 'index']);
Route::get('/boarding-cards/{boarding_card}', [BoardingCardController::class, 'get'])->missing([BoardingCardService::class, 'missing']);
Route::post('/boarding-cards', [BoardingCardController::class, 'store']);
Route::put('/boarding-cards/{boarding_card}', [BoardingCardController::class, 'update'])->missing([BoardingCardService::class, 'missing']);
Route::delete('/boarding-cards/{boarding_card}', [BoardingCardController::class, 'destroy'])->missing([BoardingCardService::class, 'missing']);
