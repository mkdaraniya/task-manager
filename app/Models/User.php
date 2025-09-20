<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'avatar',
        'phone',
        'address',
        'timezone',
        'social_links',
        'bio',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'social_links' => 'array',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class, 'user_id');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_user');
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function isOnline()
    {
        return cache()->has('user-is-online-' . $this->id);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_user')->withPivot('role');
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? Storage::url($this->avatar) : asset('images/default-avatar.png');
    }
}
