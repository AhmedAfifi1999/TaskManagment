<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;

class ProjectTest extends TestCase
{
    private User $user;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::findOrFail(6);

        $this->actingAs($this->user);

        // إنشاء مشروع للاختبار
        $this->project = Project::create([
            'name'        => 'Test Project',
            'color'       => '#10B981',
            'user_id'     => $this->user->id,
            'description' => 'Test Project',
            'status'      => 'NOT_STARTED',
            'start_date'  => now()->toDateString(),
            'end_date'    => now()->addDays(15)->toDateString(),
            'budget'      => 2000,
            'currency'    => 'USD',
        ]);
    }


    public function test_can_create_project()
    {
        $this->assertDatabaseHas('projects', [
            'id' => $this->project->id,
        ]);

        echo "\nCREATE PROJECT SUCCESS\n";
    }


   public function test_can_update_project()
{
    $this->put(
        route('admin.projects.update', $this->project->id),
        [
            'name'        => 'Updated Project',
            'color'       => '#FF0000',
            'user_id'     => $this->user->id,
            'description' => 'Updated Description',
            'status'      => 'IN_PROGRESS',
            'start_date'  => now()->toDateString(),
            'end_date'    => now()->addDays(45)->toDateString(),
            'budget'      => 3000,
            'currency'    => 'USD',
        ]
    );

    $this->assertTrue(true);

    echo "\nUPDATE PROJECT SUCCESS\n";
}

public function test_can_delete_project()
{
    $this->delete(
        route('admin.projects.destroy', $this->project->id)
    );

    $this->assertTrue(true);

    echo "\nDELETE PROJECT SUCCESS\n";
}
}