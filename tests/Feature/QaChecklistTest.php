<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QaChecklistTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Project $project;

    private Issue $issue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->project = Project::factory()->create(['user_id' => $this->user->id]);
        $this->issue = Issue::factory()->for($this->project)->create();
    }

    public function test_resource_routes_return_correct_views(): void
    {
        $this->actingAs($this->user)
            ->get(route('projects.index'))
            ->assertOk()
            ->assertViewIs('projects.index');

        $this->actingAs($this->user)
            ->get(route('projects.create'))
            ->assertOk()
            ->assertViewIs('projects.create');

        $this->actingAs($this->user)
            ->get(route('projects.show', $this->project))
            ->assertOk()
            ->assertViewIs('projects.show');

        $this->actingAs($this->user)
            ->get(route('projects.edit', $this->project))
            ->assertOk()
            ->assertViewIs('projects.edit');

        $this->actingAs($this->user)
            ->get(route('projects.issues.index', $this->project))
            ->assertOk()
            ->assertViewIs('issues.index');

        $this->actingAs($this->user)
            ->get(route('projects.issues.create', $this->project))
            ->assertOk()
            ->assertViewIs('issues.create');

        $this->actingAs($this->user)
            ->get(route('issues.show', $this->issue))
            ->assertOk()
            ->assertViewIs('issues.show');

        $this->actingAs($this->user)
            ->get(route('issues.edit', $this->issue))
            ->assertOk()
            ->assertViewIs('issues.edit');

        $this->actingAs($this->user)
            ->get(route('tags.index'))
            ->assertOk()
            ->assertViewIs('tags.index');
    }

    public function test_ajax_issue_list_returns_partial(): void
    {
        $this->actingAs($this->user)
            ->get(route('projects.issues.index', $this->project), [
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->assertOk()
            ->assertViewIs('issues._list');
    }

    public function test_comments_index_returns_json(): void
    {
        Comment::factory()->for($this->issue)->create();

        $this->actingAs($this->user)
            ->getJson(route('issues.comments.index', $this->issue))
            ->assertOk()
            ->assertJsonStructure(['data', 'next_page_url']);
    }

    public function test_comment_store_returns_201_json(): void
    {
        $this->actingAs($this->user)
            ->postJson(route('issues.comments.store', $this->issue), [
                'body' => 'Looks good.',
            ])
            ->assertCreated()
            ->assertJson([
                'comment' => [
                    'author_name' => $this->user->name,
                    'body' => 'Looks good.',
                ],
            ]);
    }

    public function test_comment_store_returns_422_for_invalid_data(): void
    {
        $this->actingAs($this->user)
            ->postJson(route('issues.comments.store', $this->issue), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['body']);
    }

    public function test_tag_attach_and_detach_return_json(): void
    {
        $tag = Tag::factory()->create();

        $this->actingAs($this->user)
            ->postJson(route('issues.tags.attach', [$this->issue, $tag]))
            ->assertOk()
            ->assertJson(['attached' => true, 'tag' => ['id' => $tag->id]]);

        $this->actingAs($this->user)
            ->deleteJson(route('issues.tags.detach', [$this->issue, $tag]))
            ->assertOk()
            ->assertJson(['detached' => true]);
    }

    public function test_user_attach_and_detach_return_json(): void
    {
        $member = User::factory()->create();

        $this->actingAs($this->user)
            ->postJson(route('issues.users.attach', [$this->issue, $member]))
            ->assertOk()
            ->assertJson(['attached' => true, 'user' => ['id' => $member->id]]);

        $this->actingAs($this->user)
            ->deleteJson(route('issues.users.detach', [$this->issue, $member]))
            ->assertOk()
            ->assertJson(['detached' => true]);
    }

    public function test_form_validation_errors_show_inline_on_issue_create(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('projects.issues.store', $this->project), []);

        $response->assertSessionHasErrors(['title', 'status', 'priority']);
        $response->assertRedirect();
    }

    public function test_invalid_status_and_priority_are_rejected(): void
    {
        $this->actingAs($this->user)
            ->post(route('projects.issues.store', $this->project), [
                'title' => 'Bad enums',
                'status' => 'invalid',
                'priority' => 'critical',
            ])
            ->assertSessionHasErrors(['status', 'priority']);
    }

    public function test_duplicate_tag_name_is_rejected(): void
    {
        Tag::factory()->create(['name' => 'duplicate']);

        $this->actingAs($this->user)
            ->post(route('tags.store'), [
                'name' => 'duplicate',
                'color' => '#E8A1B3',
            ])
            ->assertSessionHasErrors(['name']);
    }

    public function test_deleting_project_cascades_issues_and_comments(): void
    {
        $issue = Issue::factory()->for($this->project)->create();
        Comment::factory()->for($issue)->count(2)->create();

        $issueId = $issue->id;
        $projectId = $this->project->id;

        $this->actingAs($this->user)
            ->delete(route('projects.destroy', $this->project))
            ->assertRedirect(route('projects.index'));

        $this->assertDatabaseMissing('projects', ['id' => $projectId]);
        $this->assertDatabaseMissing('issues', ['id' => $issueId]);
        $this->assertDatabaseMissing('comments', ['issue_id' => $issueId]);
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $this->get(route('projects.index'))
            ->assertRedirect(route('login'));
    }

    public function test_project_index_avoids_n_plus_one_on_issues_count(): void
    {
        Project::factory(5)->create(['user_id' => $this->user->id]);

        \Illuminate\Support\Facades\DB::enableQueryLog();

        $this->actingAs($this->user)->get(route('projects.index'))->assertOk();

        $queries = \Illuminate\Support\Facades\DB::getQueryLog();
        $issueCountQueries = collect($queries)->filter(
            fn ($q) => str_contains(strtolower($q['query']), 'issues') && str_contains(strtolower($q['query']), 'count')
        )->count();

        $this->assertLessThanOrEqual(2, $issueCountQueries, 'Expected a single withCount query, not N+1');
    }

    public function test_issue_status_update_via_kanban_returns_json(): void
    {
        $this->issue->update(['status' => 'open']);

        $this->actingAs($this->user)
            ->patchJson(route('issues.status.update', $this->issue), ['status' => 'in_progress'])
            ->assertOk()
            ->assertJson([
                'status' => 'in_progress',
                'progress' => 55,
            ]);

        $this->assertDatabaseHas('issues', [
            'id' => $this->issue->id,
            'status' => 'in_progress',
        ]);

        $this->assertDatabaseHas('issue_activities', [
            'issue_id' => $this->issue->id,
            'action' => 'status_changed',
            'from_value' => 'open',
            'to_value' => 'in_progress',
        ]);
    }

    public function test_user_cannot_edit_another_users_issue(): void
    {
        $other = User::factory()->create();

        $this->actingAs($other)
            ->get(route('issues.edit', $this->issue))
            ->assertForbidden();
    }

    public function test_user_cannot_edit_another_users_project(): void
    {
        $other = User::factory()->create();

        $this->actingAs($other)
            ->get(route('projects.edit', $this->project))
            ->assertForbidden();
    }

    public function test_issue_show_displays_activity_timeline(): void
    {
        \App\Models\IssueActivity::log($this->issue, $this->user, 'comment_added');

        $this->actingAs($this->user)
            ->get(route('issues.show', $this->issue))
            ->assertOk()
            ->assertSee('Activity')
            ->assertSee('Added a comment');
    }

    public function test_activities_index_returns_json(): void
    {
        \App\Models\IssueActivity::log($this->issue, $this->user, 'created');

        $this->actingAs($this->user)
            ->getJson(route('issues.activities.index', $this->issue))
            ->assertOk()
            ->assertJsonStructure(['data', 'next_page_url']);
    }

    public function test_owner_can_delete_issue_from_show(): void
    {
        $this->actingAs($this->user)
            ->delete(route('issues.destroy', $this->issue))
            ->assertRedirect(route('projects.show', $this->project));

        $this->assertDatabaseMissing('issues', ['id' => $this->issue->id]);
    }

    public function test_owner_can_delete_comment(): void
    {
        $comment = Comment::factory()->for($this->issue)->create([
            'author_name' => $this->user->name,
        ]);

        $this->actingAs($this->user)
            ->deleteJson(route('comments.destroy', $comment))
            ->assertOk()
            ->assertJson(['deleted' => true]);

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
