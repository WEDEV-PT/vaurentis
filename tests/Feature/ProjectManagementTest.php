<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectClick;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_administrator_can_access_project_management(): void
    {
        $administrator = User::factory()->create(['is_admin' => true]);

        $this->actingAs($administrator)
            ->get('/app/projects')
            ->assertOk();

        $this->actingAs($administrator)
            ->get('/app/projects/create')
            ->assertOk();

        $this->actingAs($administrator)
            ->get('/app/users/create')
            ->assertOk();
    }

    public function test_an_administrator_can_edit_their_profile_and_application_branding(): void
    {
        $administrator = User::factory()->create(['is_admin' => true]);

        $this->actingAs($administrator)
            ->get('/app/profile')
            ->assertOk()
            ->assertSee('Application branding')
            ->assertSee('Application logo')
            ->assertSee('Time zone');
    }

    public function test_the_saved_application_logo_is_served_without_a_public_storage_symlink(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('branding/logo.png', 'logo-image-content');

        User::factory()->create([
            'is_admin' => true,
            'brand_logo_path' => 'branding/logo.png',
        ]);

        $this->get(route('branding.logo'))
            ->assertOk()
            ->assertHeader('content-disposition', 'inline; filename="Vaurentis logo"')
            ->assertStreamedContent('logo-image-content');
    }

    public function test_projects_can_be_assigned_to_users(): void
    {
        $project = Project::query()->create([
            'name' => 'Website institucional',
            'slug' => 'website-institucional',
            'status' => 'active',
        ]);
        $user = User::factory()->create();

        $project->users()->attach($user);

        $this->assertTrue($project->users()->whereKey($user)->exists());
        $this->assertTrue($user->projects()->whereKey($project)->exists());

        $category = Category::query()->create([
            'name' => 'Estratégia',
            'slug' => 'estrategia',
        ]);
        $project->categories()->attach($category);

        $this->assertTrue($project->categories()->whereKey($category)->exists());
    }

    public function test_an_active_user_without_an_available_project_sees_the_empty_projects_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/app/my-projects')
            ->assertOk()
            ->assertSee('No projects assigned.');
    }

    public function test_administrator_sees_analytics_grouped_by_project(): void
    {
        $administrator = User::factory()->create(['is_admin' => true]);
        $project = Project::query()->create([
            'name' => 'Analytics Board',
            'slug' => 'analytics-board',
            'status' => 'active',
        ]);

        $this->actingAs($administrator)
            ->get('/app/analytics')
            ->assertOk()
            ->assertSee('Analytics Board');
    }

    public function test_administrator_can_view_the_audit_log(): void
    {
        $administrator = User::factory()->create(['is_admin' => true]);

        $this->actingAs($administrator)
            ->get('/app/audit-logs')
            ->assertOk()
            ->assertSee('Audit Log');
    }

    public function test_audit_log_dates_are_displayed_in_the_administrators_timezone(): void
    {
        $administrator = User::factory()->create([
            'is_admin' => true,
            'timezone' => 'Europe/Lisbon',
        ]);

        AuditLog::query()->create([
            'action' => 'project.created',
            'target_type' => 'Project',
            'target_label' => 'Timezone test',
            'occurred_at' => Carbon::parse('2026-07-01 12:00:00', 'UTC'),
        ]);

        $this->actingAs($administrator)
            ->get('/app/audit-logs')
            ->assertOk()
            ->assertSee('01/07/2026 13:00');
    }

    public function test_an_assigned_user_can_record_a_project_click(): void
    {
        $project = Project::query()->create([
            'name' => 'Tracked Board',
            'slug' => 'tracked-board',
            'html_content' => '<h1>Tracked Board</h1>',
            'is_published' => true,
            'status' => 'active',
        ]);
        $user = User::factory()->create();
        $project->users()->attach($user);
        $trackingToken = Str::random(40);

        $this->actingAs($user)
            ->withSession(["project-click-tracking.{$project->id}" => $trackingToken])
            ->postJson(route('project-clicks.store', ['project' => $project]), [
                'tracking_token' => $trackingToken,
                'x' => 3250,
                'y' => 7500,
            ])
            ->assertNoContent();

        $this->assertDatabaseHas('project_clicks', [
            'project_id' => $project->id,
            'user_id' => $user->id,
            'click_x' => 3250,
            'click_y' => 7500,
        ]);
    }

    public function test_administrator_can_view_a_project_heat_map(): void
    {
        $administrator = User::factory()->create(['is_admin' => true]);
        $clickingUser = User::factory()->create(['name' => 'Heat Map User']);
        $project = Project::query()->create([
            'name' => 'Heat Map Board',
            'slug' => 'heat-map-board',
            'html_content' => '<h1>Heat Map Board</h1>',
            'is_published' => true,
            'status' => 'active',
        ]);
        $project->users()->attach($clickingUser);
        ProjectClick::query()->create([
            'project_id' => $project->id,
            'user_id' => $clickingUser->id,
            'click_x' => 5000,
            'click_y' => 5000,
            'clicked_at' => now(),
        ]);

        $this->actingAs($administrator)
            ->get("/app/analytics/{$project->id}/heat-map")
            ->assertOk()
            ->assertSee('Heat Map Board')
            ->assertSee('Total clicks')
            ->assertSee('Clicks by user')
            ->assertSee('All users')
            ->assertSee('Heat Map User');
    }

    public function test_an_assigned_user_can_view_a_published_project_in_the_back_office(): void
    {
        $project = Project::query()->create([
            'name' => 'Board Vaurentis',
            'slug' => 'board-vaurentis',
            'html_content' => '<h1>Board Vaurentis</h1>',
            'is_published' => true,
            'status' => 'active',
        ]);
        $user = User::factory()->create();
        $project->users()->attach($user);

        $this->actingAs($user)
            ->get("/app/projects/view/{$project->id}")
            ->assertOk()
            ->assertSee('Board Vaurentis');

        $this->assertDatabaseHas('project_accesses', [
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get('/app/projects')
            ->assertForbidden();

        $this->actingAs($user)
            ->get('/app/users')
            ->assertForbidden();
    }

    public function test_an_administrator_can_preview_a_project_without_creating_analytics(): void
    {
        $administrator = User::factory()->create(['is_admin' => true]);
        $project = Project::query()->create([
            'name' => 'Draft board',
            'slug' => 'draft-board',
            'html_content' => '<h1>Draft board</h1>',
            'is_published' => false,
            'status' => 'archived',
        ]);

        $this->actingAs($administrator)
            ->get("/app/projects/{$project->id}/preview")
            ->assertOk()
            ->assertSee('Admin preview')
            ->assertSee('Draft board');

        $this->assertDatabaseCount('project_accesses', 0);
        $this->assertDatabaseCount('project_clicks', 0);
    }

    public function test_an_unassigned_user_cannot_view_a_project(): void
    {
        $project = Project::query()->create([
            'name' => 'Private Board',
            'slug' => 'private-board',
            'html_content' => '<h1>Private Board</h1>',
            'is_published' => true,
            'status' => 'active',
        ]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get("/app/projects/view/{$project->id}")
            ->assertForbidden();
    }

    public function test_an_anonymous_project_request_is_redirected_and_audited(): void
    {
        $project = Project::query()->create([
            'name' => 'Protected Board',
            'slug' => 'protected-board',
            'html_content' => '<h1>Protected Board</h1>',
            'is_published' => true,
            'status' => 'active',
        ]);

        $this->get("/app/projects/view/{$project->id}")
            ->assertRedirect('/app/login');

        $this->assertDatabaseHas('audit_logs', [
            'project_id' => $project->id,
            'action' => 'access.unauthenticated',
            'actor_id' => null,
        ]);
    }

    public function test_an_assigned_user_can_download_a_project_source_file(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('project-source-files/project-data.csv', "name,value\nExample,42\n");

        $project = Project::query()->create([
            'name' => 'Downloadable board',
            'slug' => 'downloadable-board',
            'html_content' => '<h1>Downloadable board</h1>',
            'source_file_path' => 'project-source-files/project-data.csv',
            'source_filename' => 'project-data.csv',
            'is_published' => true,
            'status' => 'active',
        ]);
        $user = User::factory()->create();
        $project->users()->attach($user);

        $this->actingAs($user)
            ->get("/app/projects/view/{$project->id}")
            ->assertOk()
            ->assertSee('Download CSV');

        $this->actingAs($user)
            ->get(route('projects.source-file.download', ['project' => $project]))
            ->assertOk()
            ->assertDownload('project-data.csv');

        $administrator = User::factory()->create(['is_admin' => true]);

        $this->actingAs($administrator)
            ->get("/app/projects/{$project->id}/preview")
            ->assertOk()
            ->assertSee('Download CSV');
    }

    public function test_a_source_file_only_project_is_not_available_to_an_assigned_user(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('project-source-files/source-only.csv', "name\nExample\n");

        $project = Project::query()->create([
            'name' => 'Source only project',
            'slug' => 'source-only-project',
            'source_file_path' => 'project-source-files/source-only.csv',
            'source_filename' => 'source-only.csv',
            'is_published' => true,
            'status' => 'active',
        ]);
        $user = User::factory()->create();
        $project->users()->attach($user);

        $this->actingAs($user)
            ->get("/app/projects/view/{$project->id}")
            ->assertNotFound();
    }
}
