<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     *
     */
    protected $fillable = [
        'name',
        'description',
        'color',
        'user_id',
        'status',
        'start_date',
        'end_date',
    ];

    /**
     * Get all projects.
     *
     */
    public static function getAllProjects($search = null)
    {
        $query = static::query();

        // If there's a search criteria, filter projects based on it
        if ($search !== null) {
            $query->whereNull('deleted_at');
            $query->where('name', 'like', '%' . $search . '%');
            $query->orderBy('id', 'DESC');
        }

        return $query->get();
    }

    /**
     * Create a new project.
     *
     */
    public static function createProject(array $data)
    {
        return static::create($data);
    }

    /**
     * Update a project.
     *
     */
    public function updateProject(array $data)
    {
        return $this->update($data);
    }

    /**
     * Soft delete a project.
     *
     */
    public function deleteProject()
    {
        return $this->delete();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function team()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project');
    }
}
