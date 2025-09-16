<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Json\AttendanceEmployeeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('employee/attendance/{id}/{date}',[AttendanceEmployeeController::class,'index']);

Route::post('employee/attendance',[AttendanceEmployeeController::class,'empAttendance'])->name('json.emp.attendance');
// Route for getting departments and employees by branch

Route::get('get-departments-and-employee-by-branch', [AttendanceEmployeeController::class, 'getDepartmentsByBranch'])->name('getDepartmentsByBranch');

// Route for getting employees by department
Route::get('get-employee-by-department', [AttendanceEmployeeController::class, 'getEmployeeByDepartment'])->name('getEmployeeByDepartment');

