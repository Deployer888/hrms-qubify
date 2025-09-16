<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name(),
            'dob' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'employee_id' => $this->faker->unique()->numerify('EMP####'),
            'branch_id' => 1,
            'department_id' => 1,
            'designation_id' => 1,
            'company_doj' => Carbon::now()->subMonths(6)->format('Y-m-d'),
            'documents' => null,
            'account_holder_name' => $this->faker->name(),
            'account_number' => $this->faker->bankAccountNumber(),
            'bank_name' => $this->faker->company(),
            'bank_identifier_code' => $this->faker->swiftBicNumber(),
            'branch_location' => $this->faker->city(),
            'tax_payer_id' => $this->faker->numerify('TAX####'),
            'salary_type' => 'Monthly',
            'salary' => $this->faker->numberBetween(30000, 100000),
            'is_active' => 1,
            'is_probation' => 0,
            'is_team_leader' => 0,
            'paid_leave_balance' => 20.0,
            'created_by' => 1,
        ];
    }

    public function probation()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_probation' => 1,
                'paid_leave_balance' => 0.0,
            ];
        });
    }

    public function teamLeader()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_team_leader' => 1,
            ];
        });
    }
}