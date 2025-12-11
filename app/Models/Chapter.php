<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'description',
        'position',
        'is_free',
        'is_published',
        'created_by',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_published' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function (Chapter $chapter) {
            if (empty($chapter->slug)) {
                $chapter->slug = Str::slug($chapter->title);
            }

            if (empty($chapter->position)) {
                // set position as next available position for the course
                $max = self::where('course_id', $chapter->course_id)->max('position');
                $chapter->position = $max ? $max + 1 : 1;
            }
        });
    }

    // Relations
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('position');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
