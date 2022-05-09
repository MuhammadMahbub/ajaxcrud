<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('employee', [EmployeeController::class, 'index'])->name('employee');
Route::post('/employee', [EmployeeController::class, 'store']);
Route::get('/employees_show', [EmployeeController::class, 'show']);
Route::get('/employee_edit/{id}', [EmployeeController::class,'edit']);
Route::put('/employee_update/{id}', [EmployeeController::class,'update']);
Route::delete('/employee_delete/{id}', [EmployeeController::class,'destroy']);
