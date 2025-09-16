<?php

namespace Database\Factories;

use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class LeaveFactory extends Factory
{
    protected $model = Leave::class;

    public function definition()
    {
        return [
            'employee_id' => Employee::factory(),
            'leave_type_id' => LeaveType::factory(),
            'applied_on' => Carbon::now()->format('Y-m-d'),
            'start_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'total_leave_days' => 2,
            'leave_reason' => $this->faker->sentence(10),
            'remark' => $this->faker->sentence(5),
            'status' => 'Pending',
            'leavetype' => 'full',
            'created_by' => 1,
        ];
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Pending',
            ];
        });
    }

    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Approve',
            ];
        });
    }

    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Reject',
                'reject_reason' => $this->faker->sentence(10),
            ];
        });
    }

    public function halfDay()
    {
        return $this->state(function (array $attributes) {
            return [
                'leavetype' => 'half',
                'total_leave_days' => 0.5,
                'day_segment' => $this->faker->randomElement(['morning', 'afternoon']),
                'is_halfday' => 1,
            ];
        });
    }

    public function shortLeave()
    {
        return $this->state(function (array $attributes) {
            return [
                'leavetype' => 'short',
                'total_leave_days' => 0.25,
                'start_time' => '09:00',
                'end_time' => '11:00',
            ];
        });
    }
}