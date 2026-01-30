<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    public $incrementing = false;

    public $timestamps = true;

    protected $fillable = [
        'teacher_id',
        'stream_id',
        'subject_id',
        'class_id',
        'date',
        'time',
        'status',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
