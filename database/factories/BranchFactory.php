<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition()
    {
        return [
            'branch_name' => $this->faker->company,
            'branch_description' => $this->faker->text,
            'branch_status' => 'active', // You can modify this as needed
            'branch_address' => $this->faker->address,
            'branch_phone' => $this->faker->phoneNumber,
            'branch_email' => $this->faker->email,
            'branch_website' => $this->faker->url,
            'monthly_budget' => $this->faker->randomFloat(2, 1000, 100000),
        ];
    }
}
