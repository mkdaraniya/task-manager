<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Board extends Model
{
    protected $guarded = [];
    protected $fillable = ['name', 'slug', 'project_id', 'description', 'status'];

    protected static function booted()
    {
        static::creating(function ($board) {
            if (empty($board->slug)) {
                $slug = Str::slug($board->name);
                $original = $slug;
                $counter = 1;

                while (self::where('slug', $slug)->exists()) {
                    $slug = $original . '-' . $counter;
                    $counter++;
                }

                $board->slug = $slug;
            }
        });
    }

    public static function generateSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function statuses()
    {
        return $this->hasMany(Status::class)->orderBy('position');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
