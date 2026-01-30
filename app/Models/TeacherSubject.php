<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'class_id',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
