<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }
    }
public function viewAny(User $user)
{
    return $user->can('view projects') 
        || $user->can('view own projects');
}

public function view(User $user, Project $project)
{
    if ($user->can('view projects')) {
        return true;
    }

    return $user->can('view own projects') &&
           ($project->user_id === $user->id ||
            $project->team()->where('user_id', $user->id)->exists());
}


    public function create(User $user)
    {
        return $user->can('create project');
    }

public function update(User $user, Project $project)
{
    if ($user->can('edit project')) {
        return true;
    }

    // إذا عنده صلاحية تعديل المشاريع الخاصة به
    return $user->can('edit own project') && $project->user_id == $user->id;
}

  public function delete(User $user, Project $project)
{
    if ($user->can('delete project')) {
        return true;
    }

    return $user->can('delete own project') && $project->user_id == $user->id;
}
}
