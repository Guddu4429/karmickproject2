<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_code',
        'designation',
        'email',
        'phone',
    ];

    /**
     * Get the user that owns the teacher.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subjects assigned to the teacher.
     */
    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class);
    }
}
