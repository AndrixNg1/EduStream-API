<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonStream extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id','quality','file_path','container','mime','filesize'
    ];

    public function lesson() { return $this->belongsTo(Lesson::class); }
}
