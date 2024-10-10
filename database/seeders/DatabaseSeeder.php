<?php

namespace Database\Seeders;

use App\Models\People;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->peopleSeed();
    }

    private function peopleSeed(): void
    {
        People::factory(8)
            ->create([
                'active' => true,
            ]);

        People::factory(2)
            ->create([
                'active' => false,
            ]);
    }
}
