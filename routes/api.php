<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FCMTokenController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AadhaarController;
use App\Http\Controllers\Api\Dashboard\DashboardController;
use App\Http\Controllers\Api\Employee\EmployeeController as EmpController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\Attendance\AttendanceController as AttController;
use App\Http\Controllers\Api\Staff\StaffController;
use App\Http\Controllers\Api\Staff\RoleController;
use App\Http\Controllers\Api\Hr\AwardController;
use App\Http\Controllers\Api\Hr\TransferController;
use App\Http\Controllers\Api\Hr\ResignationController;
use App\Http\Controllers\Api\Hr\PromotionController;
use App\Http\Controllers\Api\Hr\ComplaintController;
use App\Http\Controllers\Api\Hr\WarningController;
use App\Http\Controllers\Api\Hr\TerminationController;
use App\Http\Controllers\Api\Hr\HolidayController;
use App\Http\Controllers\Api\Policy\CompanyPolicyController;
use App\Http\Controllers\Api\Ticket\TicketController;
use App\Http\Controllers\Api\Leave\LeaveController as NewLeaveController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\KioskController;
use App\Http\Controllers\Api\{KioskAdminController, AppVersionController, OfficeController, EmployeeLocationController, NotificationController, AttendanceController};

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
//api module
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forget-password', [AuthController::class, 'forgetPassword']);


Route::get('/get-all-employees', [EmployeeController::class, 'getAllEmployees']);

Route::get('/get-all-app-versions', [AppVersionController::class, 'index']);
Route::post('/add-app-versions', [AppVersionController::class, 'store']);
Route::post('/app-version/{package}', [AppVersionController::class, 'update']);

Route::get('/offices', [OfficeController::class, 'index']);
Route::get('/office-details/overview/{id}', [OfficeController::class, 'detailOverview']);
Route::get('/office-details/employees/{id}', [OfficeController::class, 'detailsEmployees']);
Route::get('/office-details/attendance/{id}', [OfficeController::class, 'detailsAttendance']);
Route::get('/office-details/department/{id}', [OfficeController::class, 'detailsDepartment']);
Route::get('/office/employee-details/{id}', [OfficeController::class, 'getEmployeeDetails']);

Route::get('/employee-location', [EmployeeLocationController::class, 'index']);
Route::post('/store-employee-location', [EmployeeLocationController::class, 'store']);

// Kiosk
Route::post('/kiosk/admin/login', [KioskAdminController::class, 'adminLogin']);
// KIOSK Admin Routes
Route::post('mark-attendance-kiosk', [KioskController::class, 'markAttendanceFromKiosk']);

Route::group(['middleware' => ['auth:api']],function(){
    
    // Protected routes (require authentication KIOSK)
    Route::get('/kiosk/admin/attendance-logs', [KioskAdminController::class, 'getAttendanceLogs']);
    Route::get('/kiosk/admin/dashboard', [KioskAdminController::class, 'getAttendanceDashboard']);
    Route::post('/kiosk/admin/logout', [KioskAdminController::class, 'logout']);


    // Attendance 
    Route::get('get-attendance', [AttController::class, 'getEmployeeList']);
    Route::get('attendance-statistics', [AttController::class, 'getEmployeeStatistics']);
    Route::post('attendace-clock', [AttendanceEmployeeController::class, 'attendanceClock']);
    
    //dashboard
    Route::get('dashboard',[DashboardController::class,'dashboard']);
    Route::post('send-otp', [AadhaarController::class, 'send_otp']);
    Route::post('verify-otp', [AadhaarController::class, 'verify_otp_post']);
    Route::post('aadhaar-detail', [AadhaarController::class, 'addhaar_detail']);
    
    //staff
    Route::get('staff/user/role',[StaffController::class,'role']);
    Route::get('staff/user',[StaffController::class,'user']);
    Route::post('staff/user',[StaffController::class,'store']);
    Route::put('staff/user/{id}',[StaffController::class,'update']);
    Route::delete('staff/delete/user/{id}',[StaffController::class,'delete']);
    Route::put('staff/password-change/user/{id}',[StaffController::class,'changePass']);

    Route::get('staff/role',[RoleController::class,'index']);
    Route::get('staff/role/permission-list',[RoleController::class,'permissionList']);
    Route::post('staff/role',[RoleController::class,'store']);
    Route::put('staff/role/{id}',[RoleController::class,'update']);
    Route::delete('staff/delete/role/{id}',[RoleController::class,'delete']);
    
    //employee
    Route::get('employee',[EmpController::class,'employee']);
    Route::get('employee/last-login', [EmpController::class, 'lastLogin']);
    Route::get('employee/{id}',[EmpController::class,'show']);
    Route::get('/employee/{id}/active-inactive/{action}',[EmpController::class,'activeInactive']);
    Route::delete('employee/destroy/{id}',[EmpController::class,'destroy']);
    Route::post('/employee-pin', [EmpController::class, 'employeePin']);
    Route::get('/reset-employee-pin', [EmpController::class, 'resetEmployeePin']);
    Route::post('/employee', [EmpController::class, 'store']);
    Route::post('/update-employee/{id}', [EmpController::class, 'update']);
    Route::get('/employee-with-department/{id}', [EmpController::class, 'employeeWithDepartment']);
    Route::get('/employee-department-with-branch/{id}', [EmpController::class, 'employeeAndDepartment']);

    //hr award
    Route::get('/hr/awards', [AwardController::class, 'index']);
    Route::get('/hr/awards/create', [AwardController::class, 'create']);
    Route::post('/hr/awards', [AwardController::class, 'store']);
    Route::get('/hr/awards/{award}', [AwardController::class, 'show']);
    Route::get('/hr/awards/{award}/edit', [AwardController::class, 'edit']);
    Route::put('/hr/awards/{award}', [AwardController::class, 'update']);
    Route::delete('/hr/awards/{award}', [AwardController::class, 'destroy']);

    //hr transfer
    Route::get('/hr/transfers', [TransferController::class, 'index']);
    Route::get('/hr/transfers/create', [TransferController::class, 'create']);
    Route::post('/hr/transfers', [TransferController::class, 'store']);
    Route::get('/hr/transfers/{transfer}', [TransferController::class, 'show']);
    Route::get('/hr/transfers/{transfer}/edit', [TransferController::class, 'edit']);
    Route::put('/hr/transfers/{transfer}', [TransferController::class, 'update']);
    Route::delete('/hr/transfers/{transfer}', [TransferController::class, 'destroy']);

    //hr rasignation
    Route::get('/hr/rasignation', [ResignationController::class, 'index']);
    Route::get('/hr/rasignation/create', [ResignationController::class, 'create']);
    Route::post('/hr/rasignation', [ResignationController::class, 'store']);
    Route::get('/hr/rasignation/{id}', [ResignationController::class, 'show']);
    Route::get('/hr/rasignation/{id}/edit', [ResignationController::class, 'edit']);
    Route::put('/hr/rasignation/{id}', [ResignationController::class, 'update']);
    Route::delete('/hr/rasignation/{id}', [ResignationController::class, 'destroy']);

    //hr promotion
    Route::get('/hr/promotion', [PromotionController::class, 'index']);
    Route::get('/hr/promotion/create', [PromotionController::class, 'create']);
    Route::post('/hr/promotion', [PromotionController::class, 'store']);
    Route::get('/hr/promotion/{id}', [PromotionController::class, 'show']);
    Route::get('/hr/promotion/{id}/edit', [PromotionController::class, 'edit']);
    Route::put('/hr/promotion/{id}', [PromotionController::class, 'update']);
    Route::delete('/hr/promotion/{id}', [PromotionController::class, 'destroy']);

    //hr complain
    Route::get('/hr/complaint', [ComplaintController::class, 'index']);
    Route::get('/hr/complaint/create', [ComplaintController::class, 'create']);
    Route::post('/hr/complaint', [ComplaintController::class, 'store']);
    Route::get('/hr/complaint/{id}', [ComplaintController::class, 'show']);
    Route::get('/hr/complaint/{id}/edit', [ComplaintController::class, 'edit']);
    Route::put('/hr/complaint/{id}', [ComplaintController::class, 'update']);
    Route::delete('/hr/complaint/{id}', [ComplaintController::class, 'destroy']);

    //hr complain
    Route::get('/hr/warning', [WarningController::class, 'index']);
    Route::get('/hr/warning/create', [WarningController::class, 'create']);
    Route::post('/hr/warning', [WarningController::class, 'store']);
    Route::get('/hr/warning/{id}', [WarningController::class, 'show']);
    Route::get('/hr/warning/{id}/edit', [WarningController::class, 'edit']);
    Route::put('/hr/warning/{id}', [WarningController::class, 'update']);
    Route::delete('/hr/warning/{id}', [WarningController::class, 'destroy']);

    //hr termination
    Route::get('/hr/termination', [TerminationController::class, 'index']);
    Route::get('/hr/termination/create', [TerminationController::class, 'create']);
    Route::post('/hr/termination', [TerminationController::class, 'store']);
    Route::get('/hr/termination/{id}', [TerminationController::class, 'show']);
    Route::get('/hr/termination/{id}/edit', [TerminationController::class, 'edit']);
    Route::put('/hr/termination/{id}', [TerminationController::class, 'update']);
    Route::delete('/hr/termination/{id}', [TerminationController::class, 'destroy']);

    //hr Holiday
    Route::get('/hr/holidays', [HolidayController::class, 'index']);
    Route::post('/hr/holidays', [HolidayController::class, 'store']);
    Route::get('/hr/holidays/{holiday}', [HolidayController::class, 'show']);
    Route::put('/hr/holidays/{holiday}', [HolidayController::class, 'update']);
    Route::delete('/hr/holidays/{holiday}', [HolidayController::class, 'destroy']);

    //Ticket
    Route::get('/ticket', [TicketController::class, 'index']);
    Route::post('/ticket', [TicketController::class, 'store']);
    Route::post('/ticket/reply', [TicketController::class, 'changereply']);
    Route::get('/ticket/reply/{id}', [TicketController::class, 'reply']);
    Route::get('/ticket/create', [TicketController::class, 'create']);
    Route::get('/ticket/{id}', [TicketController::class, 'show']);
    Route::get('/ticket/{id}/edit', [TicketController::class, 'edit']);
    Route::put('/ticket/{id}', [TicketController::class, 'update']);
    Route::delete('/ticket/{id}', [TicketController::class, 'destroy']);

    //policy company-policy
    Route::get('/policy/company-policy', [CompanyPolicyController::class, 'index']);
    Route::get('/policy/company-policy/create', [CompanyPolicyController::class, 'create']);
    Route::post('/policy/company-policy', [CompanyPolicyController::class, 'store']);
    Route::get('/policy/company-policy/{id}', [CompanyPolicyController::class, 'show']);
    Route::get('/policy/company-policy/{id}/edit', [CompanyPolicyController::class, 'edit']);
    Route::post('/policy/company-policy/{id}/update', [CompanyPolicyController::class, 'update']);
    Route::delete('/policy/company-policy/{id}', [CompanyPolicyController::class, 'destroy']);
    Route::post('/policy/company-policy/acknowledge', [CompanyPolicyController::class, 'acknowledge']);
    Route::get('/policy/company-policy/acknowledge/{id}', [CompanyPolicyController::class, 'showAcknowledge']);

    Route::get('/leaves', [NewLeaveController::class, 'index']);
    Route::get('/leaves-type/{id}', [NewLeaveController::class, 'getLeaveTypes']);
    Route::get('/leaves/create', [NewLeaveController::class, 'create']);
    Route::post('/leaves', [NewLeaveController::class, 'store']);
    Route::get('/leaves/{id}/edit', [NewLeaveController::class, 'edit']);
    Route::put('/leaves/{id}', [NewLeaveController::class, 'update']);
    Route::delete('/leaves/{id}', [NewLeaveController::class, 'destroy']);
    Route::post('/leave/changeaction', [NewLeaveController::class, 'changeaction']);
    
    // Employee Balance API
    Route::get('/employee-leave-balances', [EmployeeBalanceController::class, 'getEmployeeLeaveBalances']);
    
    Route::post('/send-notification', [NotificationController::class, 'sendToDevice']);
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread', [NotificationController::class, 'unread']);
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);
    
});

//end api module

Route::post('/save-fcm-token', [FCMTokenController::class, 'store']);

// https://hrm.qubifytech.com/public/api/documentation
Route::get('/api/documentation', function () {
    return view('swagger.index');
});




Route::group(['middleware' => ['auth:api']], function () {

    Route::get('profile', [UserController::class, 'profile']);
    Route::get('user', [UserController::class, 'profile']);
    // Update Last Login
    Route::post('/update-last-login', [AuthController::class, 'updateLastLogin']);

    //User
    Route::get('/get-user/{id}', [UserController::class, 'getUser']);

    // Employees
    Route::get('/get-employee/{id}', [EmployeeController::class, 'getEmployee']);
    Route::get('/employees', [EmployeeController::class, 'getEmployees']);
    // Route::get('/delete-employee', [EmployeeController::class, 'destroy']);
    // Route::get('/import-employees', [EmployeeController::class, 'import']);
    Route::get('/employee-profile', [EmployeeController::class, 'profile']);
    Route::get('/show-employee/{id}', [EmployeeController::class, 'show']);
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
    Route::post('attendance', [AttendanceController::class, 'attendance']);
    Route::get('/attendance-current-timer-state', [AttendanceController::class, 'currentTimeAttendance']);
    Route::post('/get-today-attendance', [AttendanceController::class, 'getTodayAttendance']);
    Route::post('/employee-data-cum-list', [AttendanceController::class, 'getEmployeeDataCumList']);
    Route::post('/current-timer-state', [AttendanceController::class, 'currentTimerState']);

    /* Employee Attendance */
    Route::post('/employee-attendance-update/{id}', [AttendanceEmployeeController::class, 'update']);
    Route::post('/employee-attendance-create', [AttendanceEmployeeController::class, 'attendance']);
    Route::post('/employee-attendance-update/{id}', [AttendanceEmployeeController::class, 'update']);
    // Route::post('/employee-attendance-create', [AttendanceEmployeeController::class, 'attendance']);
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
    Route::post('/edit-profile-pic', [UserController::class, 'updateProfile']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/fcm-token', [AuthController::class, 'addFcmToken']);
});
