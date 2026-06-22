<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-3 months', 'now');
        $deadline = fake()->dateTimeBetween($startDate, '+1 month');

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->paragraph(),
            'start_date' => $startDate,
            'deadline' => $deadline,
        ];
    }
}
