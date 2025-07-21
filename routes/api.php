<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudyController;
use App\Http\Controllers\Api\KeywordController;

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

// Route for fetching all studies with their keywords and metadata
Route::get('/studies', [StudyController::class, 'index']);

// Route for fetching the hierarchical keyword structure
Route::get('/keywords-hierarchy', [KeywordController::class, 'hierarchy']);
