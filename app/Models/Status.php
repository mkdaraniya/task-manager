<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['board_id', 'name', 'slug', 'color', 'position', 'is_default'];

    // Automatically generate slug on creating
    protected static function booted()
    {
        static::creating(function ($status) {
            if (empty($status->slug)) {
                $slug = Str::slug($status->name);
                $original = $slug;
                $counter = 1;

                // Ensure slug is unique within the same board
                while (self::where('board_id', $status->board_id)->where('slug', $slug)->exists()) {
                    $slug = $original . '-' . $counter;
                    $counter++;
                }

                $status->slug = $slug;
            }
        });
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
