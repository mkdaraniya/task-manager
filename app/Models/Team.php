<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Team extends Model
{
    use HasFactory, HasRoles;

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class, 'team_user')->withPivot('role');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
