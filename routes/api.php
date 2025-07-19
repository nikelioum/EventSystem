<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventsController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegistrationsController;


//Get all Events
Route::get('/events', [EventsController::class, 'index']);
//Get single Event
Route::get('/events/{id}', [EventsController::class, 'show']);


//Get User Token via login
Route::post('login', [LoginController::class, 'getToken']);

//Registrations Manage for logged in users
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/events/{id}/register', [RegistrationsController::class, 'register']);
    Route::delete('/events/{id}/unregister', [RegistrationsController::class, 'unregister']);
    Route::get('/user/registrations', [RegistrationsController::class, 'index']);
});



// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
