<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
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
Route::get('demo-plan/{type}','TestController@demoPlan')->name('demo.plan.actve');
Route::get('my-test','TestController@test');

Route::get('/', function () {
    $cookiePlan = request()->cookie('plan_type');
    if(!$cookiePlan)
    {
        Cookie::queue('plan_type', 2, 30); // expires in 10 minutes
    }
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::post('/update-organization-info', 'UserController@updateOrganizationInfo')->name('update.organization.info');

require __DIR__ . '/auth.php';

Route::get('/check', 'HomeController@check')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::get('approve-leave-by-mail/{id}','LeaveController@approveLeave')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
)->name('leave.approve');
Route::get('reject-leave-by-mail/{id}','LeaveController@rejectLeave')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ])->name('leave.reject');

Route::get('/password/resets/{lang?}', 'Auth\LoginController@showLinkRequestForm')->name('change.langPass');

Route::get('/', 'HomeController@index')->name('home')->middleware(['XSS']);
Route::get('/home', 'HomeController@index')->name('home')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::get('/home/getlanguvage', 'HomeController@getlanguvage')->name('home.getlanguvage');

Route::group(
    [
        'middleware' => [
            'auth','subs_plan',
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
            'auth','subs_plan',
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
        'auth','subs_plan',
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
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
// End

Route::resource('user', 'UserController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan','subs_plan'
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
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('employee-profile', 'EmployeeController@profile')->name('employee.profile')->middleware(
    [
        'auth','subs_plan',
        'XSS',
    ]
);
Route::get('show-employee-profile/{id}', 'EmployeeController@profileShow')->name('show.employee.profile')->middleware(
    [
        'auth','subs_plan',
        'XSS',
    ]
);
Route::get('lastlogin', 'EmployeeController@lastLogin')->name('lastlogin')->middleware(
    [
        'auth','subs_plan',
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
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
)->name('employee.exit-employee');
Route::get('get-my-team', 'EmployeeController@getMyTeam')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
)->name('employee.team-members');
Route::get('team/employee/{id}', 'EmployeeController@show')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
)->name('employee.member.show');
Route::get('member-leaves', 'EmployeeController@memberLeaves')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
)->name('employee.member-leaves');

Route::get('employee-deactivate/{id}', 'EmployeeController@employeeDeactivateLeaves')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
)->name('employee.deactivate');

Route::get('employee-activate/{id}', 'EmployeeController@employeeActivateLeaves')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
)->name('employee.activate');

// Route::get('employee/{type?}', 'EmployeeController@index')->name('employee.index');

Route::resource('employee', 'EmployeeController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);



Route::resource('department', 'DepartmentController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('designation', 'DesignationController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('document', 'DocumentController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('branch', 'BranchController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('awardtype', 'AwardTypeController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('award', 'AwardController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('termination', 'TerminationController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('terminationtype', 'TerminationTypeController')->middleware(
    [
        'auth','subs_plan',
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
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::get('holiday/calender', 'HolidayController@calender')->name('holiday.calender')->middleware(
    [
        'auth','subs_plan',
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
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('allowances/create/{eid}', 'AllowanceController@allowanceCreate')->name('allowances.create')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('commissions/create/{eid}', 'CommissionController@commissionCreate')->name('commissions.create')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('loans/create/{eid}', 'LoanController@loanCreate')->name('loans.create')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('saturationdeductions/create/{eid}', 'SaturationDeductionController@saturationdeductionCreate')->name('saturationdeductions.create')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('otherpayments/create/{eid}', 'OtherPaymentController@otherpaymentCreate')->name('otherpayments.create')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('overtimes/create/{eid}', 'OvertimeController@overtimeCreate')->name('overtimes.create')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


//payslip

Route::resource('paysliptype', 'PayslipTypeController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('allowance', 'AllowanceController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('commission', 'CommissionController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('allowanceoption', 'AllowanceOptionController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('loanoption', 'LoanOptionController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('deductionoption', 'DeductionOptionController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('loan', 'LoanController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('saturationdeduction', 'SaturationDeductionController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('otherpayment', 'OtherPaymentController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('overtime', 'OvertimeController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::post('event/getdepartment', 'EventController@getdepartment')->name('event.getdepartment')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::post('event/getemployee', 'EventController@getemployee')->name('event.getemployee')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('event', 'EventController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('import/event/file', 'EventController@importFile')->name('event.file.import');
Route::post('import/event', 'EventController@import')->name('event.import');
Route::get('export/event', 'EventController@export')->name('event.export');

Route::post('meeting/getdepartment', 'MeetingController@getdepartment')->name('meeting.getdepartment')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::post('meeting/getemployee', 'MeetingController@getemployee')->name('meeting.getemployee')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('meeting', 'MeetingController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::post('employee/update/sallary/{id}', 'SetSalaryController@employeeUpdateSalary')->name('employee.salary.update')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('salary/employeeSalary', 'SetSalaryController@employeeSalary')->name('employeesalary')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('setsalary', 'SetSalaryController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::get('payslip/paysalary/{id}/{date}', 'PaySlipController@paysalary')->name('payslip.paysalary')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('payslip/bulk_pay_create/{date}', 'PaySlipController@bulk_pay_create')->name('payslip.bulk_pay_create')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::post('payslip/bulkpayment/{date}', 'PaySlipController@bulkpayment')->name('payslip.bulkpayment')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::post('payslip/search_json', 'PaySlipController@search_json')->name('payslip.search_json')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('payslip/employeepayslip', 'PaySlipController@employeepayslip')->name('payslip.employeepayslip')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('payslip/showemployee/{id}', 'PaySlipController@showemployee')->name('payslip.showemployee')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('payslip/editemployee/{id}', 'PaySlipController@editemployee')->name('payslip.editemployee')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::post('payslip/editemployee/{id}', 'PaySlipController@updateEmployee')->name('payslip.updateemployee')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('payslip/pdf/{id}/{m}', 'PaySlipController@pdf')->name('payslip.pdf')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('payslip/payslipPdf/{id}', 'PaySlipController@payslipPdf')->name('payslip.payslipPdf')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('payslip/send/{id}/{m}', 'PaySlipController@send')->name('payslip.send')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::get('payslip/delete/{id}', 'PaySlipController@destroy')->name('payslip.delete')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('payslip', 'PaySlipController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('resignation', 'ResignationController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('travel', 'TravelController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('promotion', 'PromotionController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('transfer', 'TransferController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('complaint', 'ComplaintController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('warning', 'WarningController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::get('profile', 'UserController@profile')->name('profile')->middleware(
    [
        'auth','subs_plan',
        'XSS',
    ]
);
Route::post('edit-profile', 'UserController@editprofile')->name('update.account')->middleware(
    [
        'auth','subs_plan',
        'XSS',
    ]
);


Route::resource('accountlist', 'AccountListController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('accountbalance', 'AccountListController@account_balance')->name('accountbalance')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::get('leave/{id}/action', 'LeaveController@action')->name('leave.action')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('leave/{id}/reason', 'LeaveController@showReason')->name('leave.reason')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::post('leave/changeaction', 'LeaveController@changeaction')->name('leave.changeaction')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::post('leave/jsoncount', 'LeaveController@jsoncount')->name('leave.jsoncount')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('leave/get-paid-leave-balance/{id}', 'LeaveController@getPaidLeaveBalance')->name('leave.paidleavebalance')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('leave/get-leave-balance', 'LeaveController@getLeaveBalance')->name('leave.getleavebalance')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('leave/check-existing-leave', 'LeaveController@checkExistingLeave')->name('leave.checkexistingleave')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('leave', 'LeaveController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);



Route::get('ticket/{id}/reply', 'TicketController@reply')->name('ticket.reply')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::post('ticket/changereply', 'TicketController@changereply')->name('ticket.changereply')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('ticket', 'TicketController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::get('attendanceemployee/bulkattendance', 'AttendanceEmployeeController@bulkAttendance')->name('attendanceemployee.bulkattendance')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::post('attendanceemployee/bulkattendance', 'AttendanceEmployeeController@bulkAttendanceData')->name('attendanceemployee.bulkattendance')->middleware(
    [
        'auth','subs_plan',
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
        'auth','subs_plan',
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
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
)->name('attendanceemployee.copy');

Route::get('/copy/attendance', 'AttendanceEmployeeController@newCopy')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('attendanceemployee', 'AttendanceEmployeeController')->middleware(
    [
        'auth',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('timesheet', 'TimeSheetController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('expensetype', 'ExpenseTypeController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('incometype', 'IncomeTypeController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('paymenttype', 'PaymentTypeController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('leavetype', 'LeaveTypeController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('payees', 'PayeesController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('payer', 'PayerController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('deposit', 'DepositController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('expense', 'ExpenseController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('transferbalance', 'TransferBalanceController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::group(
    [
        'middleware' => [
            'auth','subs_plan',
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
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('permissions', 'PermissionController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::get('user/{id}/plan', 'UserController@upgradePlan')->name('plan.upgrade')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('user/{id}/plan/{pid}', 'UserController@activePlan')->name('plan.active')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('plans', 'PlanController')->middleware(
    [
        'auth','subs_plan',
        'XSS',
    ]
);
Route::get('/plan_request/{code}', 'PlanController@plan_request')->name('plan_request')->middleware(
    [
        'auth','subs_plan',
        'XSS',
    ]
);


Route::resource('plan_requests', 'PlanRequestController');

Route::get('/plan_requests/update/{id}', 'PlanRequestController@update')->name('plan_request.update')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);



Route::post('change-password', 'UserController@updatePassword')->name('update.password');

Route::resource('coupons', 'CouponController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('account-assets', 'AssetController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('document-upload', 'DucumentUploadController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('indicator', 'IndicatorController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('appraisal', 'AppraisalController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('goaltype', 'GoalTypeController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('goaltracking', 'GoalTrackingController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('company-policy', 'CompanyPolicyController')->middleware(
    [
        'auth','subs_plan',
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
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('trainer', 'TrainerController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::post('training/status', 'TrainingController@updateStatus')->name('training.status')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::resource('training', 'TrainingController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::post('plan-pay-with-paypal', 'PaypalController@planPayWithPaypal')->name('plan.pay.with.paypal')->middleware(
    [
        'auth','subs_plan',
        'XSS',
    ]
);
Route::get('{id}/plan-get-payment-status', 'PaypalController@planGetPaymentStatus')->name('plan.get.payment.status')->middleware(
    [
        'auth','subs_plan',
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
        'auth','subs_plan',
        'XSS',
    ]
);


Route::get('report/income-expense', 'ReportController@incomeVsExpense')->name('report.income-expense')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('report/leave', 'ReportController@leave')->name('report.leave')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('employee/{id}/leave/{status}/{type}/{month}/{year}', 'ReportController@employeeLeave')->name('report.employee.leave')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('report/account-statement', 'ReportController@accountStatement')->name('report.account.statement')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('report/payroll', 'ReportController@payroll')->name('report.payroll')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('report/monthly/attendance', 'ReportController@monthlyAttendance')->name('report.monthly.attendance')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::get('report/attendance/{month}/{branch}/{department}', 'ReportController@exportCsv')->name('report.attendance')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::get('report/timesheet', 'ReportController@timesheet')->name('report.timesheet')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


//------------------------------------  Recurtment --------------------------------

Route::resource('job-category', 'JobCategoryController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('job-stage', 'JobStageController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::post('job-stage/order', 'JobStageController@order')->name('job.stage.order')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('job', 'JobController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('career/{id}/{lang}', 'JobController@career')->name('career');
Route::get('job/requirement/{code}/{lang}', 'JobController@jobRequirement')->name('job.requirement');
Route::get('job/apply/{code}/{lang}', 'JobController@jobApply')->name('job.apply');
Route::post('job/apply/data/{code}', 'JobController@jobApplyData')->name('job.apply.data');


Route::get('job-application/candidate', 'JobApplicationController@candidate')->name('job.application.candidate')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('job-application', 'JobApplicationController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::post('job-application/order', 'JobApplicationController@order')->name('job.application.order')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::post('job-application/{id}/rating', 'JobApplicationController@rating')->name('job.application.rating')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::delete('job-application/{id}/archive', 'JobApplicationController@archive')->name('job.application.archive')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::post('job-application/{id}/skill/store', 'JobApplicationController@addSkill')->name('job.application.skill.store')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::post('job-application/{id}/note/store', 'JobApplicationController@addNote')->name('job.application.note.store')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::delete('job-application/{id}/note/destroy', 'JobApplicationController@destroyNote')->name('job.application.note.destroy')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::post('job-application/getByJob', 'JobApplicationController@getByJob')->name('get.job.application')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::get('job-onboard', 'JobApplicationController@jobOnBoard')->name('job.on.board')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('job-onboard/create/{id}', 'JobApplicationController@jobBoardCreate')->name('job.on.board.create')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::post('job-onboard/store/{id}', 'JobApplicationController@jobBoardStore')->name('job.on.board.store')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::get('job-onboard/edit/{id}', 'JobApplicationController@jobBoardEdit')->name('job.on.board.edit')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::post('job-onboard/update/{id}', 'JobApplicationController@jobBoardUpdate')->name('job.on.board.update')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::delete('job-onboard/delete/{id}', 'JobApplicationController@jobBoardDelete')->name('job.on.board.delete')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('job-onboard/convert/{id}', 'JobApplicationController@jobBoardConvert')->name('job.on.board.convert')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::post('job-onboard/convert/{id}', 'JobApplicationController@jobBoardConvertData')->name('job.on.board.convert')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::post('job-application/stage/change', 'JobApplicationController@stageChange')->name('job.application.stage.change')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('custom-question', 'CustomQuestionController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);


Route::resource('interview-schedule', 'InterviewScheduleController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);
Route::get('interview-schedule/create/{id?}', 'InterviewScheduleController@create')->name('interview-schedule.create')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

//================================= Custom Landing Page ====================================//

Route::get('/landingpage', 'LandingPageSectionController@index')->name('custom_landing_page.index')->middleware(['auth','subs_plan', 'XSS', 'CheckPlan']);
Route::get('/LandingPage/show/{id}', 'LandingPageSectionController@show');
Route::post('/LandingPage/setConetent', 'LandingPageSectionController@setConetent')->middleware(['auth','subs_plan', 'XSS', 'CheckPlan']);
Route::get('/get_landing_page_section/{name}', function ($name) {
    $plans = \DB::table('plans')->get();

    return view('custom_landing_page.' . $name, compact('plans'));
});
Route::post('/LandingPage/removeSection/{id}', 'LandingPageSectionController@removeSection')->middleware(['auth','subs_plan', 'XSS']);
Route::post('/LandingPage/setOrder', 'LandingPageSectionController@setOrder')->middleware(['auth','subs_plan', 'XSS']);
Route::post('/LandingPage/copySection', 'LandingPageSectionController@copySection')->middleware(['auth','subs_plan', 'XSS']);


//================================= Payment Gateways  ====================================//

Route::post('/plan-pay-with-paystack', ['as' => 'plan.pay.with.paystack', 'uses' => 'PaystackPaymentController@planPayWithPaystack'])->middleware(['auth','subs_plan', 'XSS']);
Route::get('/plan/paystack/{pay_id}/{plan_id}', ['as' => 'plan.paystack', 'uses' => 'PaystackPaymentController@getPaymentStatus']);

Route::post('/plan-pay-with-flaterwave', ['as' => 'plan.pay.with.flaterwave', 'uses' => 'FlutterwavePaymentController@planPayWithFlutterwave'])->middleware(['auth','subs_plan', 'XSS']);
Route::get('/plan/flaterwave/{txref}/{plan_id}', ['as' => 'plan.flaterwave', 'uses' => 'FlutterwavePaymentController@getPaymentStatus']);

Route::post('/plan-pay-with-razorpay', ['as' => 'plan.pay.with.razorpay', 'uses' => 'RazorpayPaymentController@planPayWithRazorpay'])->middleware(['auth','subs_plan', 'XSS']);
Route::get('/plan/razorpay/{txref}/{plan_id}', ['as' => 'plan.razorpay', 'uses' => 'RazorpayPaymentController@getPaymentStatus']);

Route::post('/plan-pay-with-paytm', ['as' => 'plan.pay.with.paytm', 'uses' => 'PaytmPaymentController@planPayWithPaytm'])->middleware(['auth','subs_plan', 'XSS']);
Route::post('/plan/paytm/{plan}', ['as' => 'plan.paytm', 'uses' => 'PaytmPaymentController@getPaymentStatus']);

Route::post('/plan-pay-with-mercado', ['as' => 'plan.pay.with.mercado', 'uses' => 'MercadoPaymentController@planPayWithMercado'])->middleware(['auth','subs_plan', 'XSS']);
Route::post('/plan/mercado', ['as' => 'plan.mercado', 'uses' => 'MercadoPaymentController@getPaymentStatus']);

Route::post('/plan-pay-with-mollie', ['as' => 'plan.pay.with.mollie', 'uses' => 'MolliePaymentController@planPayWithMollie'])->middleware(['auth','subs_plan', 'XSS']);
Route::get('/plan/mollie/{plan}', ['as' => 'plan.mollie', 'uses' => 'MolliePaymentController@getPaymentStatus']);

Route::post('/plan-pay-with-skrill', ['as' => 'plan.pay.with.skrill', 'uses' => 'SkrillPaymentController@planPayWithSkrill'])->middleware(['auth','subs_plan', 'XSS']);
Route::get('/plan/skrill/{plan}', ['as' => 'plan.skrill', 'uses' => 'SkrillPaymentController@getPaymentStatus']);

Route::post('/plan-pay-with-coingate', ['as' => 'plan.pay.with.coingate', 'uses' => 'CoingatePaymentController@planPayWithCoingate'])->middleware(['auth','subs_plan', 'XSS']);
Route::get('/plan/coingate/{plan}', ['as' => 'plan.coingate', 'uses' => 'CoingatePaymentController@getPaymentStatus']);

Route::post('paymentwall', ['as' => 'paymentwall', 'uses' => 'PaymentWallPaymentController@paymentwall']);
Route::post('plan-pay-with-paymentwall/{plan}', ['as' => 'plan.pay.with.paymentwall', 'uses' => 'PaymentWallPaymentController@planPayWithPaymentwall']);
Route::any('/plan/{flag}', 'PaymentWallPaymentController@paymenterror')->name('callback.error');
// Route::get('/plans/{flag}', ['as' => 'error.plan.show','uses' => 'PaymentWallPaymentController@planeerror']);


Route::resource('competencies', 'CompetenciesController')->middleware(
    [
        'auth','subs_plan',
        'XSS', 'CheckPlan'
    ]
);

Route::resource('performanceType', 'PerformanceTypeController')->middleware(
    [
        'auth','subs_plan',
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
Route::any('zoommeeting/calendar', 'ZoomMeetingController@calender')->name('zoom_meeting.calender')->middleware(['auth','subs_plan', 'XSS']);
Route::resource('zoom-meeting', 'ZoomMeetingController')->middleware(['auth','subs_plan', 'XSS']);

//slack
Route::post('setting/slack', 'SettingsController@slack')->name('slack.setting');

//telegram
Route::post('setting/telegram', 'SettingsController@telegram')->name('telegram.setting');

//twilio
Route::post('setting/twilio', 'SettingsController@twilio')->name('twilio.setting');

// recaptcha
Route::post('/recaptcha-settings',['as' => 'recaptcha.settings.store','uses' =>'SettingsController@recaptchaSettingStore'])->middleware(['auth','subs_plan','XSS']);

// user reset password
Route::any('user-reset-password/{id}', 'UserController@employeePassword')->name('user.reset');
Route::post('user-reset-password/{id}', 'UserController@employeePasswordReset')->name('user.password.update');

// Route::any('getContacts', 'App\Http\Controllers\vendor\Chatify\MessagesController@getContacts');

Route::get('/documentation', function () {
    return view('swagger.index');
});


Route::group(['middleware' => 'auth'],function(){
    Route::post('/update-fcm-token', 'UserController@updateFcmToken')->name('update.fcm.token');
    Route::get('/notification', 'NotificationSendController@index')->name('notification');
    Route::post('/store-token', 'NotificationSendController@updateDeviceToken')->name('store.token');
    Route::post('/send-web-notification', 'NotificationSendController@sendNotification')->name('send.web-notification');
});
