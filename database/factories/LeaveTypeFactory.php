<?php

namespace Database\Factories;

use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveTypeFactory extends Factory
{
    protected $model = LeaveType::class;

    public function definition()
    {
        return [
            'title' => $this->faker->randomElement(['Sick Leave', 'Casual Leave', 'Paid Leave', 'Maternity Leave']),
            'days' => $this->faker->numberBetween(5, 20),
            'created_by' => 1,
        ];
    }

    public function sickLeave()
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => 'Sick Leave',
                'days' => 10,
            ];
        });
    }

    public function paidLeave()
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => 'Paid Leave',
                'days' => 20,
            ];
        });
    }
}