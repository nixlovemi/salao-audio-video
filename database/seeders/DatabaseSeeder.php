<?php

namespace Database\Seeders;

use App\Models\People;
use App\Models\Attendance;
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
        $this->attendanceSeed();
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

    private function attendanceSeed(): void
    {
        // need to loop through 10 random dates
        $datesYmd = [];
        for ($i = 0; $i < 10; $i++) {
            $days = ($i + 1) * 7;
            $datesYmd[] = date('Y-m-d', strtotime("-$days days"));
        }

        // loop dates
        foreach ($datesYmd as $dateYmd) {
            $responsabilities = array_keys(Attendance::RESPONSABILITIES);
            foreach ($responsabilities as $responsability) {
                // get one different person per responsability
                $person = People::where('active', true)
                    ->whereNotIn('id', Attendance::where('meeting_date', $dateYmd)
                        ->pluck('people_id')
                    )
                    ->inRandomOrder()
                    ->first();

                // create attendance
                Attendance::factory()
                    ->create([
                        'people_id' => $person->id,
                        'meeting_date' => $dateYmd,
                        'responsability' => $responsability,
                    ]);
            }
        }
    }
}
