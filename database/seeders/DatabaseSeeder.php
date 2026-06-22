<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $team = User::factory()->createMany([
            ['name' => 'Alex Rivera', 'email' => 'alex@example.com'],
            ['name' => 'Jordan Lee', 'email' => 'jordan@example.com'],
            ['name' => 'Sam Patel', 'email' => 'sam@example.com'],
        ]);

        $users = collect([$admin, ...$team]);

        $projects = $users->flatMap(function (User $user) {
            return Project::factory(1)->create(['user_id' => $user->id]);
        });

        if ($projects->count() < 4) {
            $projects = $projects->merge(
                Project::factory(4 - $projects->count())->create(['user_id' => $admin->id])
            );
        }

        $tags = collect([
            ['name' => 'bug', 'color' => '#E8A1B3'],
            ['name' => 'feature', 'color' => '#9CB88D'],
            ['name' => 'design', 'color' => '#B8D8BA'],
            ['name' => 'backend', 'color' => '#6B8E9B'],
            ['name' => 'urgent', 'color' => '#C0394B'],
            ['name' => 'frontend', 'color' => '#F5D98B'],
            ['name' => 'docs', 'color' => '#D8D8D0'],
            ['name' => 'refactor', 'color' => '#A8C5E2'],
        ])->map(fn (array $tag) => Tag::create($tag));

        $issues = collect();

        foreach ($projects as $project) {
            $projectIssues = Issue::factory(5)
                ->for($project)
                ->create();

            $issues = $issues->merge($projectIssues);
        }

        foreach ($issues as $issue) {
            $issue->tags()->attach(
                $tags->random(fake()->numberBetween(2, 4))->pluck('id')
            );

            $issue->users()->attach(
                $users->random(fake()->numberBetween(1, 3))->pluck('id')
            );
        }

        Comment::factory(30)
            ->sequence(fn () => ['issue_id' => $issues->random()->id])
            ->create();
    }
}
