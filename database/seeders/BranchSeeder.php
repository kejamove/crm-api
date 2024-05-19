<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Firm;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all firms
        $firms = Firm::all();

        // Create branches for each firm
        foreach ($firms as $firm) {
            Branch::factory()->count(3)->create([
                'firm' => $firm->id,
            ]);
        }
    }
}
