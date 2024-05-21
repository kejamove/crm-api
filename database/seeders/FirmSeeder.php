<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Firm;

class FirmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 5 firms
        Firm::factory()->count(15)->create();
    }
}

