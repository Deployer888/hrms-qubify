<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
// use App\Http\Controllers\PlanRequestController;

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

//Route::get('my-test','TestController@test');

/*Route::get('/', function () {
    // return view('welcome');
});*/

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return 'Application cache cleared successfully!';
});

Route::post('aadhaar/send-otp', 'AadhaarController@send_otp')
        ->name('aadhaar.send.otp');
// Route::get('aadhaar/verify-otp', 'AadhaarController@verify_otp')
//         ->name('aadhaar.verify.otp');
Route::post('aadhaar/verify-otp', 'AadhaarController@verify_otp_post')
        ->name('aadhaar.verify.otp.post');
// Route to show the face authentication page
Route::get('/aadhaar/face-authenticate', 'AadhaarController@faceAuthenticate')->name('aadhaar.face.authenticate');

// Route to process the face authentication
Route::post('/aadhaar/face-authenticate', 'AadhaarController@authenticate')->name('aadhaar.face.authenticate.post');

Route::resource('aadhaar', 'AadhaarController');


Route::get('approve-leave-by-mail/{id}','LeaveController@approveLeave')->middleware(['auth','XSS', 'CheckPlan'])->name('leave.approve');
Route::get('reject-leave-by-mail/{id}','LeaveController@rejectLeave')->middleware(['auth','XSS', 'CheckPlan'])->name('leave.reject');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/terms', function () {
    return view('front.terms');
})->name('terms');

Route::post('/update-organization-info', 'UserController@updateOrganizationInfo')->name('update.organization.info');

require __DIR__ . '/auth.php';

Route::get('/check', 'HomeController@check')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('/password/resets/{lang?}', 'Auth\LoginController@showLinkRequestForm')->name('change.langPass');

Route::get('/', 'HomeController@index')->name('welcome')->middleware(['XSS']);
Route::get('/home', 'HomeController@index')->name('home')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);



Route::get('/home/getlanguage', 'HomeController@getlanguvage')->name('home.getlanguvage');

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS', 'CheckPlan'
        ],
    ],
    function () {

        Route::resource('settings', 'SettingsController');
        Route::post('email-settings', 'SettingsController@saveEmailSettings')->name('email.settings');
        Route::post('company-settings', 'SettingsController@saveCompanySettings')->name('company.settings');
        Route::post('payment-settings', 'SettingsController@savePaymentSettings')->name('payment.settings');
        Route::post('system-settings', 'SettingsController@saveSystemSettings')->name('system.settings');
        Route::get('company-setting', 'SettingsController@companyIndex')->name('company.setting');
        Route::get('company-email-setting/{name}', 'SettingsController@updateEmailStatus')->name('company.email.setting');
        Route::post('pusher-settings', 'SettingsController@savePusherSettings')->name('pusher.settings');
        Route::post('business-setting', 'SettingsController@saveBusinessSettings')->name('business.setting');

        Route::post('zoom-settings', 'SettingsController@zoomSetting')->name('zoom.settings');

        Route::get('test-mail', 'SettingsController@testMail')->name('test.mail');
        Route::post('test-mail', 'SettingsController@testSendMail')->name('test.send.mail');

        Route::get('create/ip', 'SettingsController@createIp')->name('create.ip');
        Route::post('create/ip', 'SettingsController@storeIp')->name('store.ip');
        Route::get('edit/ip/{id}', 'SettingsController@editIp')->name('edit.ip');
        Route::post('edit/ip/{id}', 'SettingsController@updateIp')->name('update.ip');
        Route::delete('destroy/ip/{id}', 'SettingsController@destroyIp')->name('destroy.ip');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::get('/orders', 'StripePaymentController@index')->name('order.index');
        Route::get('/stripe/{code}', 'StripePaymentController@stripe')->name('stripe');
        Route::get('/stripe_request/{code}', 'StripePaymentController@stripe_request')->name('stripe_request');
        Route::post('/stripe', 'StripePaymentController@stripePost')->name('stripe.post');
    }
);

Route::get(
    '/test',
    [
        'as' => 'test.email',
        'uses' => 'SettingsController@testEmail',
    ]
)->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post(
    '/test/send',
    [
        'as' => 'test.email.send',
        'uses' => 'SettingsController@testEmailSend',
    ]
)->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
// End

Route::resource('user', 'UserController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::delete('/employee/{employee}/document/{document}/remove', 'EmployeeController@removeDocument')
    ->name('employee.document.remove')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('employee/json', 'EmployeeController@json')->name('employee.json')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('branch/employee/json', 'EmployeeController@employeeJson')->name('branch.employee.json')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('employee-profile', 'EmployeeController@profile')->name('employee.profile')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('show-employee-profile/{id}', 'EmployeeController@profileShow')->name('show.employee.profile')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('lastlogin', 'EmployeeController@lastLogin')->name('lastlogin')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('employee/get-team-leader', 'EmployeeController@getTeamLeader')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
)->name('get-team-leader');
Route::get('get-exit-employee', 'EmployeeController@getExitEmployee')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
)->name('employee.exit-employee');
Route::get('get-my-team', 'EmployeeController@getMyTeam')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
)->name('employee.team-members');
Route::get('team/employee/{id}', 'EmployeeController@show')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
)->name('employee.member.show');
Route::get('member-leaves', 'EmployeeController@memberLeaves')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
)->name('employee.member-leaves');

Route::get('employee-deactivate/{id}', 'EmployeeController@employeeDeactivateLeaves')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
)->name('employee.deactivate');

Route::get('employee-activate/{id}', 'EmployeeController@employeeActivateLeaves')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
)->name('employee.activate');

// Route::get('employee/{type?}', 'EmployeeController@index')->name('employee.index');

Route::post('/employee/departments-by-office', "EmployeeController@getDepartmentsByOffice")->name('employee.departments.by.office');
Route::post('/employee/departments-by-branch', "EmployeeController@getDepartmentsByBranch")->name('employee.departments.by.branch');
Route::post('/employee/team-leaders-by-branch', "EmployeeController@getTeamLeadersByBranch")->name('employee.team.leaders.by.branch');
Route::post('/employee/get-team-leader', "EmployeeController@getTeamLeader")->name('employee.getTeamLeader');

Route::resource('employee', 'EmployeeController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);



Route::resource('department', 'DepartmentController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('designation', 'DesignationController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('document', 'DocumentController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('branch', 'BranchController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('awardtype', 'AwardTypeController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('award', 'AwardController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('termination', 'TerminationController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('terminationtype', 'TerminationTypeController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('announcement/getdepartment', 'AnnouncementController@getdepartment')->name('announcement.getdepartment')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('announcement/getemployee', 'AnnouncementController@getemployee')->name('announcement.getemployee')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('announcement', 'AnnouncementController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::get('holiday/calender', 'HolidayController@calender')->name('holiday.calender')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('holiday', 'HolidayController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::get('employee/salary/{eid}', 'SetSalaryController@employeeBasicSalary')->name('employee.basic.salary')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('allowances/create/{eid}', 'AllowanceController@allowanceCreate')->name('allowances.create')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('commissions/create/{eid}', 'CommissionController@commissionCreate')->name('commissions.create')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('loans/create/{eid}', 'LoanController@loanCreate')->name('loans.create')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('saturationdeductions/create/{eid}', 'SaturationDeductionController@saturationdeductionCreate')->name('saturationdeductions.create')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('otherpayments/create/{eid}', 'OtherPaymentController@otherpaymentCreate')->name('otherpayments.create')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('overtimes/create/{eid}', 'OvertimeController@overtimeCreate')->name('overtimes.create')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


//payslip

Route::resource('paysliptype', 'PayslipTypeController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('allowance', 'AllowanceController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('commission', 'CommissionController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('allowanceoption', 'AllowanceOptionController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('loanoption', 'LoanOptionController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('deductionoption', 'DeductionOptionController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('loan', 'LoanController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('saturationdeduction', 'SaturationDeductionController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('otherpayment', 'OtherPaymentController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('overtime', 'OvertimeController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::post('event/getdepartment', 'EventController@getdepartment')->name('event.getdepartment')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('event/getemployee', 'EventController@getemployee')->name('event.getemployee')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('event', 'EventController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('import/event/file', 'EventController@importFile')->name('event.file.import');
Route::post('import/event', 'EventController@import')->name('event.import');
Route::get('export/event', 'EventController@export')->name('event.export');

Route::post('meeting/getdepartment', 'MeetingController@getdepartment')->name('meeting.getdepartment')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('meeting/getemployee', 'MeetingController@getemployee')->name('meeting.getemployee')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('meeting', 'MeetingController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::post('employee/update/sallary/{id}', 'SetSalaryController@employeeUpdateSalary')->name('employee.salary.update')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('salary/employeeSalary', 'SetSalaryController@employeeSalary')->name('employeesalary')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('setsalary', 'SetSalaryController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::get('payslip/paysalary/{id}/{date}', 'PaySlipController@paysalary')->name('payslip.paysalary')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('payslip/bulk_pay_create/{date}', 'PaySlipController@bulk_pay_create')->name('payslip.bulk_pay_create')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('payslip/bulkpayment/{date}', 'PaySlipController@bulkpayment')->name('payslip.bulkpayment')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('payslip/search_json', 'PaySlipController@search_json')->name('payslip.search_json')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('payslip/employeepayslip', 'PaySlipController@employeepayslip')->name('payslip.employeepayslip')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('payslip/showemployee/{id}', 'PaySlipController@showemployee')->name('payslip.showemployee')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('payslip/editemployee/{id}', 'PaySlipController@editemployee')->name('payslip.editemployee')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::post('payslip/editemployee/{id}', 'PaySlipController@updateEmployee')->name('payslip.updateemployee')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('payslip/pdf/{id}/{m}', 'PaySlipController@pdf')->name('payslip.pdf')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('payslip/payslipPdf/{id}', 'PaySlipController@payslipPdf')->name('payslip.payslipPdf')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('payslip/send/{id}/{m}', 'PaySlipController@send')->name('payslip.send')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::get('payslip/delete/{id}', 'PaySlipController@destroy')->name('payslip.delete')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('payslip', 'PaySlipController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('resignation', 'ResignationController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('travel', 'TravelController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('promotion', 'PromotionController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('transfer', 'TransferController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('complaint', 'ComplaintController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('warning', 'WarningController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::get('profile', 'UserController@profile')->name('profile')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::post('edit-profile', 'UserController@editprofile')->name('update.account')->middleware(
    [
        'auth',
        'XSS',
    ]
);


Route::resource('accountlist', 'AccountListController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('accountbalance', 'AccountListController@account_balance')->name('accountbalance')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::get('leave/{id}/action', 'LeaveController@action')->name('leave.action')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('leave/{id}/reason', 'LeaveController@showReason')->name('leave.reason')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('leave/changeaction', 'LeaveController@changeaction')->name('leave.changeaction')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('leave/jsoncount', 'LeaveController@jsoncount')->name('leave.jsoncount')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('leave/get-paid-leave-balance/{id}', 'LeaveController@getPaidLeaveBalance')->name('leave.paidleavebalance')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('leave/get-leave-balance', 'LeaveController@getLeaveBalance')->name('leave.getleavebalance')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('leave/check-existing-leave', 'LeaveController@checkExistingLeave')->name('leave.checkexistingleave')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('leave/get-employee-leave-balances', 'LeaveController@getEmployeeLeaveBalances')->name('leave.getemployeeleavebalances')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('leave', 'LeaveController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);



Route::get('ticket/{id}/reply', 'TicketController@reply')->name('ticket.reply')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('ticket/changereply', 'TicketController@changereply')->name('ticket.changereply')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('ticket', 'TicketController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);



Route::post('attendanceemployee/import', 'AttendanceEmployeeController@excelImport')->name('attendanceemployee.import')->middleware(
    [
        'XSS','auth', 'CheckPlan'
    ]
);
Route::post('attendanceemployee/import/rollback', 'AttendanceEmployeeController@rollbackAttendanceImport')->name('attendanceemployee.import.rollback')->middleware(
    [
        'XSS','auth', 'CheckPlan'
    ]
);




Route::get('attendanceemployee/bulkattendance', 'AttendanceEmployeeController@bulkAttendance')->name('attendanceemployee.bulkattendance')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('attendanceemployee/bulkattendance', 'AttendanceEmployeeController@bulkAttendanceData')->name('attendanceemployee.bulkattendance')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::post('attendanceemployee/attendance', 'AttendanceEmployeeController@attendance')->name('attendanceemployee.attendance')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::GET('attendanceemployee/current-timer-state', 'AttendanceEmployeeController@currentTimeAttendance')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::get('/play-music/{id}', 'AttendanceEmployeeController@playMusic')
        ->name('attendanceemployee.play.music')
        ->middleware(
    [
    ]
);

Route::get('/copy/attendance/{id}', 'AttendanceEmployeeController@copy')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
)->name('attendanceemployee.copy');

Route::resource('attendanceemployee', 'AttendanceEmployeeController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::post('attendanceemployee/aadhaar/face-authenticate', 'AttendanceEmployeeController@attendanceAadhaarVerification')
        ->name('attendanceemployee.aadhaar.post');

Route::get('attendanceemployee/aadhaar/auth', 'AttendanceEmployeeController@attendanceAadhaar')
        ->name('attendanceemployee.aadhaar.get')
        ->middleware(
    [
        'auth',
        'XSS'
    ]
);



Route::resource('timesheet', 'TimeSheetController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('expensetype', 'ExpenseTypeController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('incometype', 'IncomeTypeController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('paymenttype', 'PaymentTypeController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('leavetype', 'LeaveTypeController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('payees', 'PayeesController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('payer', 'PayerController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('deposit', 'DepositController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('expense', 'ExpenseController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('transferbalance', 'TransferBalanceController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::group(
    [
        'middleware' => [
            'auth',
            'XSS', 'CheckPlan'
        ],
    ],
    function () {
        Route::get('change-language/{lang}', 'LanguageController@changeLanquage')->name('change.language');
        Route::get('manage-language/{lang}', 'LanguageController@manageLanguage')->name('manage.language');
        Route::post('store-language-data/{lang}', 'LanguageController@storeLanguageData')->name('store.language.data');
        Route::get('create-language', 'LanguageController@createLanguage')->name('create.language');
        Route::post('store-language', 'LanguageController@storeLanguage')->name('store.language');
        Route::delete('/lang/{lang}', 'LanguageController@destroyLang')->name('lang.destroy');
    }
);

Route::resource('roles', 'RoleController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('permissions', 'PermissionController')->middleware(
    [
        'auth',
        'XSS'
    ]
);

Route::get('user/{id}/plan', 'UserController@upgradePlan')->name('plan.upgrade')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('user/{id}/plan/{pid}', 'UserController@activePlan')->name('plan.active')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('plans', 'PlanController')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('/plan_request/{code}', 'PlanController@plan_request')->name('plan_request')->middleware(
    [
        'auth',
        'XSS',
    ]
);


Route::resource('plan_requests', 'PlanRequestController');

Route::get('/plan_requests/update/{id}', 'PlanRequestController@update')->name('plan_request.update')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);



Route::post('change-password', 'UserController@updatePassword')->name('update.password');

Route::resource('coupons', 'CouponController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('account-assets', 'AssetController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('document-upload', 'DucumentUploadController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('indicator', 'IndicatorController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('appraisal', 'AppraisalController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('goaltype', 'GoalTypeController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('goaltracking', 'GoalTrackingController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('company-policy', 'CompanyPolicyController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('acknowledge', 'CompanyPolicyController@acknowledge')->name('acknowledge.store')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('acknowledge/{id}', 'CompanyPolicyController@showAcknowledge')->name('company-policy.acknowledge')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('trainingtype', 'TrainingTypeController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('trainer', 'TrainerController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::post('training/status', 'TrainingController@updateStatus')->name('training.status')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('training', 'TrainingController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::post('plan-pay-with-paypal', 'PaypalController@planPayWithPaypal')->name('plan.pay.with.paypal')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('{id}/plan-get-payment-status', 'PaypalController@planGetPaymentStatus')->name('plan.get.payment.status')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get(
    '/apply-coupon',
    [
        'as' => 'apply.coupon',
        'uses' => 'CouponController@applyCoupon',
    ]
)->middleware(
    [
        'auth',
        'XSS',
    ]
);


Route::get('report/income-expense', 'ReportController@incomeVsExpense')->name('report.income-expense')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('report/leave', 'ReportController@leave')->name('report.leave')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('employee/{id}/leave/{status}/{type}/{month}/{year}', 'ReportController@employeeLeave')->name('report.employee.leave')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('report/account-statement', 'ReportController@accountStatement')->name('report.account.statement')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('report/payroll', 'ReportController@payroll')->name('report.payroll')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('report/monthly/attendance', 'ReportController@monthlyAttendance')->name('report.monthly.attendance')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::get('report/attendance/{month}/{branch}/{department}', 'ReportController@exportCsv')->name('report.attendance')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::get('report/timesheet', 'ReportController@timesheet')->name('report.timesheet')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


//------------------------------------  Recurtment --------------------------------

Route::resource('job-category', 'JobCategoryController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('job-stage', 'JobStageController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('job-stage/order', 'JobStageController@order')->name('job.stage.order')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('job', 'JobController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('career/{id}/{lang}', 'JobController@career')->name('career');
Route::get('job/requirement/{code}/{lang}', 'JobController@jobRequirement')->name('job.requirement');
Route::get('job/apply/{code}/{lang}', 'JobController@jobApply')->name('job.apply');
Route::post('job/apply/data/{code}', 'JobController@jobApplyData')->name('job.apply.data');


Route::get('job-application/candidate', 'JobApplicationController@candidate')->name('job.application.candidate')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('job-application', 'JobApplicationController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::post('job-application/order', 'JobApplicationController@order')->name('job.application.order')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('job-application/{id}/rating', 'JobApplicationController@rating')->name('job.application.rating')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::delete('job-application/{id}/archive', 'JobApplicationController@archive')->name('job.application.archive')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::post('job-application/{id}/skill/store', 'JobApplicationController@addSkill')->name('job.application.skill.store')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('job-application/{id}/note/store', 'JobApplicationController@addNote')->name('job.application.note.store')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::delete('job-application/{id}/note/destroy', 'JobApplicationController@destroyNote')->name('job.application.note.destroy')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('job-application/getByJob', 'JobApplicationController@getByJob')->name('get.job.application')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::get('job-onboard', 'JobApplicationController@jobOnBoard')->name('job.on.board')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('job-onboard/create/{id}', 'JobApplicationController@jobBoardCreate')->name('job.on.board.create')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('job-onboard/store/{id}', 'JobApplicationController@jobBoardStore')->name('job.on.board.store')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::get('job-onboard/edit/{id}', 'JobApplicationController@jobBoardEdit')->name('job.on.board.edit')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('job-onboard/update/{id}', 'JobApplicationController@jobBoardUpdate')->name('job.on.board.update')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::delete('job-onboard/delete/{id}', 'JobApplicationController@jobBoardDelete')->name('job.on.board.delete')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('job-onboard/convert/{id}', 'JobApplicationController@jobBoardConvert')->name('job.on.board.convert')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::post('job-onboard/convert/{id}', 'JobApplicationController@jobBoardConvertData')->name('job.on.board.convert')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::post('job-application/stage/change', 'JobApplicationController@stageChange')->name('job.application.stage.change')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('custom-question', 'CustomQuestionController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('interview-schedule', 'InterviewScheduleController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('interview-schedule/create/{id?}', 'InterviewScheduleController@create')->name('interview-schedule.create')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

//================================= Custom Landing Page ====================================//

Route::get('/landingpage', 'LandingPageSectionController@index')->name('custom_landing_page.index')->middleware(['auth', 'XSS', 'CheckPlan']);
Route::get('/LandingPage/show/{id}', 'LandingPageSectionController@show');
Route::post('/LandingPage/setConetent', 'LandingPageSectionController@setConetent')->middleware(['auth', 'XSS', 'CheckPlan']);
Route::get('/get_landing_page_section/{name}', function ($name) {
    $plans = \DB::table('plans')->get();

    return view('custom_landing_page.' . $name, compact('plans'));
});
Route::post('/LandingPage/removeSection/{id}', 'LandingPageSectionController@removeSection')->middleware(['auth', 'XSS']);
Route::post('/LandingPage/setOrder', 'LandingPageSectionController@setOrder')->middleware(['auth', 'XSS']);
Route::post('/LandingPage/copySection', 'LandingPageSectionController@copySection')->middleware(['auth', 'XSS']);


//================================= Payment Gateways  ====================================//

Route::post('/plan-pay-with-paystack', ['as' => 'plan.pay.with.paystack', 'uses' => 'PaystackPaymentController@planPayWithPaystack'])->middleware(['auth', 'XSS']);
Route::get('/plan/paystack/{pay_id}/{plan_id}', ['as' => 'plan.paystack', 'uses' => 'PaystackPaymentController@getPaymentStatus']);

Route::post('/plan-pay-with-flaterwave', ['as' => 'plan.pay.with.flaterwave', 'uses' => 'FlutterwavePaymentController@planPayWithFlutterwave'])->middleware(['auth', 'XSS']);
Route::get('/plan/flaterwave/{txref}/{plan_id}', ['as' => 'plan.flaterwave', 'uses' => 'FlutterwavePaymentController@getPaymentStatus']);

Route::post('/plan-pay-with-razorpay', ['as' => 'plan.pay.with.razorpay', 'uses' => 'RazorpayPaymentController@planPayWithRazorpay'])->middleware(['auth', 'XSS']);
Route::get('/plan/razorpay/{txref}/{plan_id}', ['as' => 'plan.razorpay', 'uses' => 'RazorpayPaymentController@getPaymentStatus']);

Route::post('/plan-pay-with-paytm', ['as' => 'plan.pay.with.paytm', 'uses' => 'PaytmPaymentController@planPayWithPaytm'])->middleware(['auth', 'XSS']);
Route::post('/plan/paytm/{plan}', ['as' => 'plan.paytm', 'uses' => 'PaytmPaymentController@getPaymentStatus']);

Route::post('/plan-pay-with-mercado', ['as' => 'plan.pay.with.mercado', 'uses' => 'MercadoPaymentController@planPayWithMercado'])->middleware(['auth', 'XSS']);
Route::post('/plan/mercado', ['as' => 'plan.mercado', 'uses' => 'MercadoPaymentController@getPaymentStatus']);

Route::post('/plan-pay-with-mollie', ['as' => 'plan.pay.with.mollie', 'uses' => 'MolliePaymentController@planPayWithMollie'])->middleware(['auth', 'XSS']);
Route::get('/plan/mollie/{plan}', ['as' => 'plan.mollie', 'uses' => 'MolliePaymentController@getPaymentStatus']);

Route::post('/plan-pay-with-skrill', ['as' => 'plan.pay.with.skrill', 'uses' => 'SkrillPaymentController@planPayWithSkrill'])->middleware(['auth', 'XSS']);
Route::get('/plan/skrill/{plan}', ['as' => 'plan.skrill', 'uses' => 'SkrillPaymentController@getPaymentStatus']);

Route::post('/plan-pay-with-coingate', ['as' => 'plan.pay.with.coingate', 'uses' => 'CoingatePaymentController@planPayWithCoingate'])->middleware(['auth', 'XSS']);
Route::get('/plan/coingate/{plan}', ['as' => 'plan.coingate', 'uses' => 'CoingatePaymentController@getPaymentStatus']);

Route::post('paymentwall', ['as' => 'paymentwall', 'uses' => 'PaymentWallPaymentController@paymentwall']);
Route::post('plan-pay-with-paymentwall/{plan}', ['as' => 'plan.pay.with.paymentwall', 'uses' => 'PaymentWallPaymentController@planPayWithPaymentwall']);
Route::any('/plan/{flag}', 'PaymentWallPaymentController@paymenterror')->name('callback.error');
// Route::get('/plans/{flag}', ['as' => 'error.plan.show','uses' => 'PaymentWallPaymentController@planeerror']);


Route::resource('competencies', 'CompetenciesController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('performanceType', 'PerformanceTypeController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);

//employee Import & Export
Route::get('import/employee/file', 'EmployeeController@importFile')->name('employee.file.import');
Route::post('import/employee', 'EmployeeController@import')->name('employee.import');
Route::get('export/employee', 'EmployeeController@export')->name('employee.export');

// Timesheet Import & Export

Route::get('import/timesheet/file', 'TimeSheetController@importFile')->name('timesheet.file.import');
Route::post('import/timesheet', 'TimeSheetController@import')->name('timesheet.import');
Route::get('export/timesheet', 'TimeSheetController@export')->name('timesheet.export');

//leave export
Route::get('export/leave', 'LeaveController@export')->name('leave.export');

//deposite Export
Route::get('export/deposite', 'DepositController@export')->name('deposite.export');

//expense Export
Route::get('export/expense', 'ExpenseController@export')->name('expense.export');

//Transfer Balance Export
Route::get('export/transfer-balance', 'TransferBalanceController@export')->name('transfer_balance.export');

//Training Import & Export
Route::get('export/training', 'TrainingController@export')->name('training.export');

//Trainer Export
Route::get('export/trainer', 'TrainerController@export')->name('trainer.export');
Route::get('import/training/file', 'TrainerController@importFile')->name('trainer.file.import');
Route::post('import/training', 'TrainerController@import')->name('trainer.import');

//Holiday Export & Import
Route::get('export/holidays', 'HolidayController@export')->name('holidays.export');
Route::get('import/holidays/file', 'HolidayController@importFile')->name('holidays.file.import');
Route::post('import/holidays', 'HolidayController@import')->name('holidays.import');

//Asset Import & Export
Route::get('export/assets', 'AssetController@export')->name('assets.export');
Route::get('import/assets/file', 'AssetController@importFile')->name('assets.file.import');
Route::post('import/assets', 'AssetController@import')->name('assets.import');

//zoom meeting
Route::any('zoommeeting/calendar', 'ZoomMeetingController@calender')->name('zoom_meeting.calender')->middleware(['auth', 'XSS']);
Route::resource('zoom-meeting', 'ZoomMeetingController')->middleware(['auth', 'XSS']);

//slack
Route::post('setting/slack', 'SettingsController@slack')->name('slack.setting');

//telegram
Route::post('setting/telegram', 'SettingsController@telegram')->name('telegram.setting');

//twilio
Route::post('setting/twilio', 'SettingsController@twilio')->name('twilio.setting');

// recaptcha
Route::post('/recaptcha-settings',['as' => 'recaptcha.settings.store','uses' =>'SettingsController@recaptchaSettingStore'])->middleware(['auth','XSS']);

// user reset password
Route::any('user-reset-password/{id}', 'UserController@employeePassword')->name('user.reset');
Route::post('user-reset-password/{id}', 'UserController@employeePasswordReset')->name('user.password.update');

Route::any('getContacts', 'App\Http\Controllers\vendor\Chatify\MessagesController@getContacts');

Route::get('/documentation', function () {
    return view('swagger.index');
});


Route::group(['middleware' => 'auth'],function(){
    Route::post('/update-fcm-token', 'UserController@updateFcmToken')->name('update.fcm.token');
    Route::get('/notification', 'NotificationSendController@index')->name('notification');
    Route::post('/store-token', 'NotificationSendController@updateDeviceToken')->name('store.token');
    Route::post('/send-web-notification', 'NotificationSendController@sendNotification')->name('send.web-notification');
});



Route::group(['middleware' => ['auth', 'XSS']], function () {
    // Office routes
    Route::get('office', 'OfficeController@index')->name('office.index');
    Route::get('office/create', 'OfficeController@create')->name('office.create');
    Route::post('office', 'OfficeController@store')->name('office.store');
    Route::get('office/{id}/edit', 'OfficeController@edit')->name('office.edit');
    Route::put('office/{id}', 'OfficeController@update')->name('office.update');
    Route::delete('office/{id}', 'OfficeController@destroy')->name('office.destroy');
    Route::get('office/{id}', 'OfficeController@show')->name('office.one.index');
});
Route::get('/office/employee/{id?}', [App\Http\Controllers\OfficeController::class, 'employee'])->name('office.employee')->middleware(['auth', 'XSS']);
Route::group(['middleware' => ['auth', 'XSS']], function () {
    // Route for displaying employee details for an office
    // Route::get('office/{id}/employee/{employeeId}', [OfficeController::class, 'employee'])->name('office.employee');
    
    // Route for getting attendance data (for charts and statistics)
    Route::get('office/{office}/attendance-data', [OfficeController::class, 'getAttendanceData'])->name('office.attendance-data');
    
    // Route for getting live employee locations
    Route::get('office/{office}/live-locations', [OfficeController::class, 'getLiveLocations'])->name('office.live-locations');
});
    

// HR Attendance Dashboard Routes
Route::get('/dash', [App\Http\Controllers\DashController::class, 'index'])->name('hr.attendance.dashboard')->middleware(['auth', 'XSS']);
Route::post('/dash-data', [App\Http\Controllers\DashController::class, 'getFilteredData'])->name('hr.attendance.data')->middleware(['auth', 'XSS']);
Route::get('/employee-locations', [App\Http\Controllers\DashController::class, 'getEmployeeGeolocations'])->name('hr.employee.locations')->middleware(['auth', 'XSS']);

Route::get('/attendance/dashboard', [App\Http\Controllers\DashController::class, 'index'])
    ->name('attendance.dashboard')
    ->middleware(['auth', 'verified']);

// Route for getting filtered data via AJAX
Route::get('/attendance/dashboard/get-filtered-data', [App\Http\Controllers\DashController::class, 'getFilteredData'])
    ->name('attendance.dashboard.filtered')
    ->middleware(['auth', 'verified']);

// Optional route for refreshing data automatically
Route::get('/attendance/dashboard/refresh-data', [App\Http\Controllers\DashController::class, 'getFilteredData'])
    ->name('attendance.dashboard.refresh')
    ->middleware(['auth', 'verified']);
