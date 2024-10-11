<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\People;
use App\Models\Attendance;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'people_id' => function() {
                return People::where('active', true)
                    ->inRandomOrder()
                    ->first();
            },
            'meeting_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'responsability' => $this->faker->randomElement(array_keys(Attendance::RESPONSABILITIES)),
            'status' => $this->faker->randomElement(array_keys(Attendance::STATUSES)),
        ];
    }
}
