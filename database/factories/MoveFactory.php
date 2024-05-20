<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Move;
use App\Enums\MoveStage;
use App\Enums\LeadSource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
    use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Move>
 */
class MoveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Move::class;

    public function definition()
    {
        return [
            'move_request_received_at' => Carbon::now(),
            'move_stage' => MoveStage::contacted,
            'lead_source' => LeadSource::offline_marketing,
            'consumer_name' => $this->faker->name,
            'corporate_name' => $this->faker->company,
            'contact_information' => $this->faker->phoneNumber,
            'moving_from' => $this->faker->city,
            'moving_to' => $this->faker->city,
            'sales_representative' => User::inRandomOrder()->first()->id,
            'branch' => Branch::inRandomOrder()->first()->id, // You can customize this based on your requirements
            'invoiced_amount' => $this->faker->randomFloat(2, 100, 1000),
            'notes' => $this->faker->text,
            'remarks' => $this->faker->paragraph,
        ];
    }
}
