<?php

namespace Database\Seeders;

use App\Models\Move;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MoveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the number of moves you want to create
        $numberOfMoves = 50;

        // Create moves using the factory
        Move::factory($numberOfMoves)->create();
    }
}
