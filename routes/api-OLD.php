<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\FCMTokenController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/save-fcm-token', [FCMTokenController::class, 'store']);

// https://hrm.qubifytech.com/public/api/documentation
Route::get('/api/documentation', function () {
    return view('swagger.index');
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    
    // Update Last Login
    Route::post('/update-last-login', [AuthController::class, 'updateLastLogin']);
    
    //User
    Route::get('/get-user/{id}', [UserController::class, 'getUser']);
    
    // Employees
    Route::get('/get-employee/{id}', [EmployeeController::class, 'getEmployee']);
    Route::get('/employees', [EmployeeController::class, 'getEmployees']);
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::post('/update-employee/{id}', [EmployeeController::class, 'update']);
    Route::get('/delete-employee', [EmployeeController::class, 'destroy']);
    // Route::get('/import-employees', [EmployeeController::class, 'import']);
    Route::get('/employee-profile', [EmployeeController::class, 'profile']);
    Route::get('/show-employee/{id}', [EmployeeController::class, 'show']);
    Route::get('/last-login', [EmployeeController::class, 'lastLogin']);
    Route::post('/employees-by-branch', [EmployeeController::class, 'employeeJson']);
    
    // Events
    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);
    Route::get('/events/{id}', [EventController::class, 'show']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    // Route::post('/events/import', [EventController::class, 'importEvent']);

    // Attendance
    Route::get('/attendance-list', [AttendanceController::class, 'index']);
    Route::get('/attendance-create', [AttendanceController::class, 'create']);
    Route::post('/attendance-store', [AttendanceController::class, 'store']);
    Route::get('/attendance-edit/{id}', [AttendanceController::class, 'edit']);
    Route::put('/attendance-update/{id}', [AttendanceController::class, 'update']);
    Route::delete('/attendance-delete/{id}', [AttendanceController::class, 'destroy']);
    Route::post('/attendance', [AttendanceController::class, 'attendance']);
    Route::get('/attendance-current-timer-state', [AttendanceController::class, 'currentTimeAttendance']);
    Route::post('/get-today-attendance', [AttendanceController::class, 'getTodayAttendance']);
    Route::post('/employee-data-cum-list', [AttendanceController::class, 'getEmployeeDataCumList']);
    Route::post('/current-timer-state', [AttendanceController::class, 'currentTimerState']);
    
    /* Employee Attendance */
    Route::post('/employee-attendance-update/{id}', [AttendanceEmployeeController::class, 'update']);
    Route::post('/employee-attendance-create', [AttendanceEmployeeController::class, 'attendance']);
    Route::get('/today-attendance/{employeeId}', [AttendanceEmployeeController::class, 'getTodayAttendance']);

    /* Announcement */
    Route::get('/announcement', [AnnouncementController::class, 'index']);
    Route::post('/get-home-announcement-data', [AnnouncementController::class, 'getHomeAnnouncementData']);
    Route::get('/announcement-create', [AnnouncementController::class, 'create']);
    Route::post('/announcement-store', [AnnouncementController::class, 'store']);
    Route::get('/announcement-detail/{id}', [AnnouncementController::class, 'announcementDetail']);
    Route::get('/announcement-edit/{id}', [AnnouncementController::class, 'edit']);
    Route::put('/announcement-update/{id}', [AnnouncementController::class, 'update']);
    Route::delete('/announcement-delete/{id}', [AnnouncementController::class, 'destroy']);
    Route::get('/announcement/getdepartment/{id}', [AnnouncementController::class, 'getDepartments']);
    Route::get('/announcement/getemployee/{id}', [AnnouncementController::class, 'getEmployees']);
    
    /* Salary */
    Route::get('/setsalary', [SetSalaryController::class, 'index']);
    Route::get('/setsalary-edit/{id}', [SetSalaryController::class, 'edit']);
    Route::get('/setsalary-show/{id}', [SetSalaryController::class, 'show']);
    Route::put('/setsalary-update/{id}', [SetSalaryController::class, 'employeeUpdateSalary']);
    Route::get('/setsalary-salary', [SetSalaryController::class, 'employeeSalary']);
    Route::get('/setsalary-basic-salary/{id}', [SetSalaryController::class, 'employeeBasicSalary']);
    
    /* Branch */
    Route::get('/branches', [BranchController::class, 'index']);
    Route::post('/branch-store', [BranchController::class, 'store']);
    Route::get('/branch/{branch}', [BranchController::class, 'edit']);
    Route::put('/branch-update/{branch}', [BranchController::class, 'update']);
    Route::delete('/branch-delete/{branch}', [BranchController::class, 'destroy']);

    /* Department */
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::post('/department-store', [DepartmentController::class, 'store']);
    Route::get('/department/{department}', [DepartmentController::class, 'edit']);
    Route::put('/department-update/{department}', [DepartmentController::class, 'update']);
    Route::delete('/department-delete/{department}', [DepartmentController::class, 'destroy']);

    /* Designation */
    Route::get('/designations', [DesignationController::class, 'index']);
    Route::post('/designation-store', [DesignationController::class, 'store']);
    Route::get('/designation/{designation}', [DesignationController::class, 'edit']);
    Route::put('/designation-update/{designation}', [DesignationController::class, 'update']);
    Route::delete('/designation-delete/{designation}', [DesignationController::class, 'destroy']);
    
    /* Leave */
    Route::get('/get-employee-leaves/{eid}', [LeaveController::class, 'index']);
    
    
    // Desktop Apis
    Route::post('/home-dashboard', [HomeController::class, 'index']);
    Route::post('/change-password', [UserController::class, 'updatePassword']);
    Route::post('/edit-profile', [UserController::class, 'editprofile']);
    
    Route::post('/logout', [AuthController::class, 'logout']);
});