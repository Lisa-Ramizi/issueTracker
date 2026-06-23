<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\IssueActivity;
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

        $alex = User::factory()->create([
            'name' => 'Alex Rivera',
            'email' => 'alex@example.com',
            'password' => 'password',
        ]);

        $jordan = User::factory()->create([
            'name' => 'Jordan Lee',
            'email' => 'jordan@example.com',
        ]);

        $sam = User::factory()->create([
            'name' => 'Sam Patel',
            'email' => 'sam@example.com',
        ]);

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

        $tag = fn (string $name) => $tags->firstWhere('name', $name);

        $sprigBilling = Project::create([
            'user_id' => $admin->id,
            'name' => 'Sprig Billing',
            'description' => 'Subscription billing and invoicing platform for B2B SaaS teams. Handles plans, proration, and Stripe sync.',
            'start_date' => '2026-03-01',
            'deadline' => '2026-08-15',
        ]);

        $pulseMetrics = Project::create([
            'user_id' => $alex->id,
            'name' => 'Pulse Metrics',
            'description' => 'Product analytics dashboard with funnels, cohort retention, and weekly KPI digests for growth teams.',
            'start_date' => '2026-04-10',
            'deadline' => '2026-09-30',
        ]);

        $sprigIssues = [
            [
                'title' => 'Stripe webhook drops subscription.updated events',
                'description' => 'Production logs show intermittent 500s on the webhook handler when a customer upgrades mid-cycle. Needs retry logic and idempotency keys.',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => '2026-06-28',
                'tags' => ['bug', 'backend', 'urgent'],
                'members' => [$alex, $jordan],
            ],
            [
                'title' => 'Prorated plan upgrade checkout flow',
                'description' => 'Design and implement the self-serve upgrade path with proration preview before payment confirmation.',
                'status' => 'open',
                'priority' => 'medium',
                'due_date' => '2026-07-12',
                'tags' => ['feature', 'frontend', 'design'],
                'members' => [$sam],
            ],
            [
                'title' => 'Export invoices as PDF from billing history',
                'description' => 'Customers need a downloadable PDF per invoice from the account billing page, including tax line items.',
                'status' => 'open',
                'priority' => 'low',
                'due_date' => '2026-07-25',
                'tags' => ['feature', 'backend'],
                'members' => [$jordan],
            ],
            [
                'title' => 'Revenue chart shows wrong timezone on dashboard',
                'description' => 'MRR chart labels shift by one day for EU customers because aggregation uses UTC instead of account timezone.',
                'status' => 'closed',
                'priority' => 'medium',
                'due_date' => '2026-06-10',
                'tags' => ['bug', 'frontend'],
                'members' => [$admin],
            ],
            [
                'title' => 'Self-serve downgrade to Starter plan',
                'description' => 'Allow customers to downgrade at period end with a confirmation modal explaining feature loss.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => '2026-07-05',
                'tags' => ['feature', 'frontend'],
                'members' => [$alex, $sam],
            ],
        ];

        $pulseIssues = [
            [
                'title' => 'Embed SDK drops events on Safari 17',
                'description' => 'Third-party cookie restrictions block the snippet on Safari. Need first-party proxy endpoint for event collection.',
                'status' => 'open',
                'priority' => 'high',
                'due_date' => '2026-06-30',
                'tags' => ['bug', 'frontend', 'urgent'],
                'members' => [$jordan, $sam],
            ],
            [
                'title' => 'Cohort retention report',
                'description' => 'Weekly cohort table showing D1, D7, and D30 retention with export to CSV for growth reviews.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => '2026-07-18',
                'tags' => ['feature', 'backend'],
                'members' => [$alex],
            ],
            [
                'title' => 'Slack digest for weekly KPI summary',
                'description' => 'Post activation rate, WAU, and top funnel drop-off to a configured Slack channel every Monday morning.',
                'status' => 'open',
                'priority' => 'low',
                'due_date' => '2026-08-01',
                'tags' => ['feature', 'backend'],
                'members' => [$admin],
            ],
            [
                'title' => 'N+1 queries on events list API',
                'description' => 'Debugbar shows duplicate queries when loading event properties. Eager load property definitions on the index endpoint.',
                'status' => 'closed',
                'priority' => 'high',
                'due_date' => '2026-06-05',
                'tags' => ['refactor', 'backend'],
                'members' => [$jordan],
            ],
        ];

        $this->seedProjectIssues($sprigBilling, $sprigIssues, $tag, $admin);
        $this->seedProjectIssues($pulseMetrics, $pulseIssues, $tag, $alex);

        $comments = [
            [
                'issue_title' => 'Stripe webhook drops subscription.updated events',
                'author' => 'Alex Rivera',
                'body' => 'Reproduced on staging with a test clock upgrade. The handler throws before the idempotency check runs.',
            ],
            [
                'issue_title' => 'Stripe webhook drops subscription.updated events',
                'author' => 'Jordan Lee',
                'body' => 'I can take the retry queue piece — we should dead-letter after three failures and alert #billing-alerts.',
            ],
            [
                'issue_title' => 'Prorated plan upgrade checkout flow',
                'author' => 'Sam Patel',
                'body' => 'Mockups are in Figma. Preview screen should show credit applied and next invoice date.',
            ],
            [
                'issue_title' => 'Embed SDK drops events on Safari 17',
                'author' => 'Sam Patel',
                'body' => 'Confirmed in BrowserStack. Events fire when the snippet loads from a subdomain on the customer site.',
            ],
            [
                'issue_title' => 'Cohort retention report',
                'author' => 'Alex Rivera',
                'body' => 'Starting with signup cohorts only. We can add payment cohorts in v2 after billing data is wired up.',
            ],
        ];

        foreach ($comments as $data) {
            $issue = Issue::query()
                ->where('title', $data['issue_title'])
                ->first();

            if ($issue) {
                Comment::create([
                    'issue_id' => $issue->id,
                    'author_name' => $data['author'],
                    'body' => $data['body'],
                ]);
            }
        }
    }

    /**
     * @param  callable(string): ?Tag  $tag
     * @param  array<int, array{title: string, description: string, status: string, priority: string, due_date: string, tags: array<int, string>, members: array<int, User>}>  $issues
     */
    private function seedProjectIssues(Project $project, array $issues, callable $tag, User $owner): void
    {
        foreach ($issues as $data) {
            $issue = $project->issues()->create([
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => $data['status'],
                'priority' => $data['priority'],
                'due_date' => $data['due_date'],
            ]);

            $issue->tags()->attach(
                collect($data['tags'])->map(fn (string $name) => $tag($name)->id)
            );

            $issue->users()->attach(
                collect($data['members'])->pluck('id')
            );

            IssueActivity::log($issue, $owner, 'created');

            if ($data['status'] !== 'open') {
                IssueActivity::log($issue, $owner, 'status_changed', from: 'open', to: $data['status']);
            }
        }
    }
}
