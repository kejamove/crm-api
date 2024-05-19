<?php
namespace Database\Factories;


use App\Models\Firm;
use Illuminate\Database\Eloquent\Factories\Factory;

class FirmFactory extends Factory
{
    protected $model = Firm::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'location' => $this->faker->company,
            'registration_number' => $this->faker->randomNumber(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

