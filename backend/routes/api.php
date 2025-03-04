<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BabyController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\VaccineController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authorization routes
Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
Route::post('/logout', [AuthController::class,'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('babies', BabyController::class);
});
// Guardian routes
Route::get('/guardians', [GuardianController::class,'getGuardians'])->middleware('auth:sanctum');
Route::get('/guardians/{id}', [GuardianController::class,'getGuardian'])->middleware('auth:sanctum');
Route::put('/guardians/{id}', [GuardianController::class,'updateGuardian'])->middleware('auth:sanctum');
Route::delete('/guardians/{id}', [GuardianController::class,'deleteGuardian'])->middleware('auth:sanctum');


// Doctor routes
Route::get('/doctors', [DoctorController::class,'index'])->middleware('auth:sanctum');
Route::get('/doctors/{id}', [DoctorController::class,'show'])->middleware('auth:sanctum');
Route::post('/doctors', [DoctorController::class,'store'])->middleware('auth:sanctum');
Route::put('/doctors/{id}', [DoctorController::class,'update'])->middleware('auth:sanctum');
Route::delete('/doctors/{doctor}', [DoctorController::class,'destroy'])->middleware('auth:sanctum');

// Appointment routes
Route::get('/appointments', [AppointmentsController::class,'index'])->middleware('auth:sanctum');
Route::get('/appointments/{id}', [AppointmentsController::class,'show'])->middleware('auth:sanctum');
Route::post('/appointments', [AppointmentsController::class,'store'])->middleware('auth:sanctum');
Route::put('/appointments/{id}', [AppointmentsController::class,'update'])->middleware('auth:sanctum');
Route::delete('/appointments/{id}', [AppointmentsController::class,'destroy'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/vaccines', [VaccineController::class, 'getVaccines']);
    Route::post('/vaccines', [VaccineController::class, 'createVaccine']);
    Route::get('/vaccines/{id}', [VaccineController::class, 'getVaccine']);
    Route::put('/vaccines/{id}', [VaccineController::class, 'updateVaccine']);
    Route::delete('/vaccines/{id}', [VaccineController::class, 'deleteVaccine']);
});

