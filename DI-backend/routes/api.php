<?php

use App\Http\Controllers\ImagesController;
use App\Http\Controllers\ImmobilienController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\UnitController;
use App\Http\Middleware\AdminPrivilege;
use App\Http\Resources\UserResource;
use App\Models\Immobilie;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


Route::get('/user', function (Request $request) {
    $user = $request->user();
    return new UserResource($user);
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/immobilien', [ImmobilienController::class, 'index'])->middleware('auth:sanctum');
Route::get('/immobilien/{id}', [ImmobilienController::class, 'retrieve'])->middleware('auth:sanctum');
Route::patch('/immobilien/{id}', [ImmobilienController::class, 'toggleHidden'])->middleware(['auth:sanctum', AdminPrivilege::class]);
Route::post('/immobilien', [ImmobilienController::class, 'create'])->middleware(['auth:sanctum', AdminPrivilege::class]);
Route::put('/immobilien/{id}', [ImmobilienController::class, 'update'])->middleware(['auth:sanctum', AdminPrivilege::class]);
Route::delete('/immobilien/{id}', [ImmobilienController::class, 'delete'])->middleware(['auth:sanctum', AdminPrivilege::class]);

Route::get('/meta/immobilien', [MetaController::class, 'immobilien'])->middleware('auth:sanctum');
Route::get('/meta/utypes', [MetaController::class, 'unitTypes'])->middleware('auth:sanctum');

Route::post('/einheiten', [UnitController::class, 'create'])->middleware(['auth:sanctum', AdminPrivilege::class]);
Route::get('/einheiten/params', [UnitController::class, 'params'])->middleware('auth:sanctum');
Route::put('/einheiten/{id}', [UnitController::class, 'update'])->middleware(['auth:sanctum', AdminPrivilege::class]);
Route::delete('/einheiten/{id}', [UnitController::class, 'delete'])->middleware(['auth:sanctum', AdminPrivilege::class]);


Route::get('/images/{owner_type}/{owner_id}', [ImagesController::class, 'retrieve'])->middleware('auth:sanctum');
Route::post('/images/{owner_type}/{owner_id}', [ImagesController::class, 'create'])->middleware(['auth:sanctum', AdminPrivilege::class]);
Route::delete('/images', [ImagesController::class, 'delete'])->middleware(['auth:sanctum', AdminPrivilege::class]);

Route::get('/test', function (Request $request) {
    
})->middleware(['auth:sanctum', AdminPrivilege::class]);