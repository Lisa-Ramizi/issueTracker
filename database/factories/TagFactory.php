<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    private const TAGS = [
        ['name' => 'bug', 'color' => '#E8A1B3'],
        ['name' => 'feature', 'color' => '#9CB88D'],
        ['name' => 'design', 'color' => '#B8D8BA'],
        ['name' => 'backend', 'color' => '#6B8E9B'],
        ['name' => 'urgent', 'color' => '#C0394B'],
        ['name' => 'frontend', 'color' => '#F5D98B'],
        ['name' => 'docs', 'color' => '#D8D8D0'],
        ['name' => 'refactor', 'color' => '#A8C5E2'],
    ];

    public function definition(): array
    {
        $tag = fake()->randomElement(self::TAGS);

        return [
            'name' => $tag['name'].'-'.fake()->unique()->numerify('###'),
            'color' => $tag['color'],
        ];
    }
}
