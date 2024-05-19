<?php
namespace Database\Factories;


use App\Models\Branch;
use App\Models\Firm;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->city,
            'firm' => Firm::factory(), // Automatically create a firm if not provided
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

