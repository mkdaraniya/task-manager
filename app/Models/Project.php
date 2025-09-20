<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'slug', 'description', 'status', 'deadline', 'is_public'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function boards()
    {
        return $this->hasMany(Board::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasManyThrough(Task::class, Board::class);
    }

    // The users() relationship was removed because the 'project_user' pivot table does not exist.
}
