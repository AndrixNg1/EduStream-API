<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id','title','slug','description',
        'type','original_path','duration','thumbnail',
        'position','is_free','is_published',
    ];

    protected static function booted()
    {
        static::creating(function (Lesson $l) {
            if (empty($l->slug)) {
                $l->slug = Str::slug($l->title) . '-' . Str::random(5);
            }
            if (empty($l->position)) {
                $max = self::where('chapter_id',$l->chapter_id)->max('position');
                $l->position = $max ? $max + 1 : 1;
            }
        });
    }

    public function chapter() { return $this->belongsTo(Chapter::class); }
    public function streams() { return $this->hasMany(LessonStream::class); }
}
