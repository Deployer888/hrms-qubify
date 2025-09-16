<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use HasApiTokens;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $settings;

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'personal_email',
        'password',
        'type',
        'avatar',
        'lang',
        'plan',
        'created_by',
        'fcm_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function currentLanguage()
    {
        return $this->lang;
    }

    public function creatorId()
    {
        if($this->type == 'company' || $this->type == 'super admin')
        {
            return $this->id;
        }
        else
        {
            return $this->created_by;
        }
    }


    public function employeeIdFormat($number)
    {
        $settings = Utility::settings();

        // return $settings["employee_prefix"] . sprintf("%d", $number);
        return $settings["employee_prefix"] . $number;
    }

    public function getBranch($branch_id)
    {
        $branch = Branch::where('id', '=', $branch_id)->first();

        return $branch;
    }

    public function getLeaveType($leave_type)
    {
        $leavetype = LeaveType::where('id', '=', $leave_type)->first();

        return $leavetype;
    }

    public function getEmployee($employee)
    {
        $employee = Employee::where('id', '=', $employee)->first();

        return $employee;
    }

    public function getDepartment($department_id)
    {
        $department = Department::where('id', '=', $department_id)->first();

        return $department;
    }

    public function getDesignation($designation_id)
    {
        $designation = Designation::where('id', '=', $designation_id)->first();

        return $designation;
    }

    public function getUser($user)
    {
        $user = User::where('id', '=', $user)->first();

        return $user;
    }

    public function userEmployee()
    {

        $userEmployee = User::select('id')->where('created_by', '=', Auth::user()->creatorId())->where('type', '=', 'employee')->get();

        return $userEmployee;
    }

    public function getUSerEmployee($id)
    {
        $employee = Employee::where('user_id', '=', $id)->first();

        return $employee;
    }

    public function priceFormat($price)
    {
        $settings = Utility::settings();

        return (($settings['site_currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . number_format($price, 2) . (($settings['site_currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
    }

    public function currencySymbol()
    {
        $settings = Utility::settings();

        return $settings['site_currency_symbol'];
    }

    public function dateFormat($date)
    {
        $settings = Utility::settings();

        return date($settings['site_date_format'], strtotime($date));
    }

    public function timeFormat($time)
    {
        $settings = Utility::settings();
        return date($settings['site_time_format'], strtotime($time));
    }

    public function getPlan()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'plan');
    }

    public function assignPlan($planID)
    {
        $plan = Plan::find($planID);
        if($plan)
        {
            $this->plan = $plan->id;
            if($plan->duration == 'month')
            {
                $this->plan_expire_date = Carbon::now()->addMonths(1)->isoFormat('YYYY-MM-DD');
            }
            elseif($plan->duration == 'year')
            {
                $this->plan_expire_date = Carbon::now()->addYears(1)->isoFormat('YYYY-MM-DD');
            }
            elseif($plan->duration == 'week')
            {
                $this->plan_expire_date = Carbon::now()->addWeeks(1)->isoFormat('YYYY-MM-DD');
            }
            elseif($plan->duration == '2_weeks')
            {
                $this->plan_expire_date = Carbon::now()->addWeeks(2)->isoFormat('YYYY-MM-DD');
            }
            else
            {
                $this->plan_expire_date = null;
            }
            $this->save();

            $users     = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'super admin')->where('type', '!=', 'company')->where('type', '!=', 'employee')->get();
            $employees = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', 'employee')->get();

            if($plan->max_users == -1)
            {
                foreach($users as $user)
                {
                    $user->is_active = 1;
                    $user->save();
                }
            }
            else
            {
                $userCount = 0;
                foreach($users as $user)
                {
                    $userCount++;
                    if($userCount <= $plan->max_users)
                    {
                        $user->is_active = 1;
                        $user->save();
                    }
                    else
                    {
                        $user->is_active = 0;
                        $user->save();
                    }
                }
            }

            if($plan->max_employees == -1)
            {
                foreach($employees as $employee)
                {
                    $employee->is_active = 1;
                    $employee->save();
                }
            }
            else
            {

                $employeeCount = 0;
                foreach($employees as $employee)
                {
                    $employeeCount++;
                    if($employeeCount <= $plan->max_employees)
                    {
                        $employee->is_active = 1;
                        $employee->save();
                    }
                    else
                    {
                        $employee->is_active = 0;
                        $employee->save();
                    }
                }
            }

            return ['is_success' => true];
        }
        else
        {
            return [
                'is_success' => false,
                'error' => 'Plan is deleted.',
            ];
        }
    }

    public function countUsers()
    {
        return User::where('type', '!=', 'super admin')->where('type', '!=', 'company')->where('type', '!=', 'employee')->where('created_by', '=', $this->creatorId())->count();
    }

    public function countEmployees()
    {
        return Employee::where('created_by', '=', $this->creatorId())->count();
    }

    public function countCompany()
    {
        return User::where('type', '=', 'company')->where('created_by', '=', $this->creatorId())->count();
    }

    public function countOrder()
    {
        return Order::count();
    }

    public function countplan()
    {
        return Plan::count();
    }

    public function countPaidCompany()
    {
        return User::where('type', '=', 'company')->whereNotIn(
            'plan', [
                      0,
                      1,
                  ]
        )->where('created_by', '=', \Auth::user()->id)->count();
    }

    public function planPrice()
    {
        $user = \Auth::user();
        if($user->type == 'super admin')
        {
            $userId = $user->id;
        }
        else
        {
            $userId = $user->created_by;
        }

        return \DB::table('settings')->where('created_by', '=', $userId)->get()->pluck('value', 'name');

    }

    public function currentPlan()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'plan');
    }


    public function unread()
    {
        return Message::where('from', '=', $this->id)->where('is_read', '=', 0)->count();
    }

    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'user_id', 'id');
    }
    
    /**
     * Get the mobile notifications for the user.
     */
    public function mobileNotifications()
    {
        return $this->hasMany(MobileNotification::class);
    }
    
    /**
     * Get unread mobile notifications count
     */
    public function unreadNotificationsCount()
    {
        return $this->mobileNotifications()->unread()->count();
    }

    /**
     * Get the dashboard type for this user
     *
     * @return string
     */
    public function getDashboardType(): string
    {
        $roleService = app(\App\Services\RoleDetectionService::class);
        return $roleService->getDashboardType($this);
    }

    /**
     * Get the user's role
     *
     * @return string
     */
    public function getUserRole(): string
    {
        $roleService = app(\App\Services\RoleDetectionService::class);
        return $roleService->getUserRole($this);
    }

    /**
     * Get permitted widgets for this user
     *
     * @return array
     */
    public function getPermittedWidgets(): array
    {
        $roleService = app(\App\Services\RoleDetectionService::class);
        return $roleService->getPermittedWidgets($this);
    }

    /**
     * Check if user can access a specific widget
     *
     * @param string $widgetType
     * @return bool
     */
    public function canAccessWidget(string $widgetType): bool
    {
        $roleService = app(\App\Services\RoleDetectionService::class);
        return $roleService->canAccessWidget($this, $widgetType);
    }

    /**
     * Check if user can access a specific dashboard type
     *
     * @param string $dashboardType
     * @return bool
     */
    public function canAccessDashboard(string $dashboardType): bool
    {
        $roleService = app(\App\Services\RoleDetectionService::class);
        return $roleService->isDashboardAccessible($this, $dashboardType);
    }

    /**
     * Check if user has HR privileges
     *
     * @return bool
     */
    public function isHRUser(): bool
    {
        $roleService = app(\App\Services\RoleDetectionService::class);
        return $roleService->isHRUser($this);
    }

    /**
     * Check if user has company privileges
     *
     * @return bool
     */
    public function isCompanyUser(): bool
    {
        $roleService = app(\App\Services\RoleDetectionService::class);
        return $roleService->isCompanyUser($this);
    }

    /**
     * Clear role cache for this user
     *
     * @return void
     */
    public function clearRoleCache(): void
    {
        $roleService = app(\App\Services\RoleDetectionService::class);
        $roleService->clearRoleCache($this);
    }
}
