<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
    protected $fillable = [
        'guardian_id',
        'admission_no',
        'roll_no',
        'first_name',
        'last_name',
        'dob',
        'gender',
        'address',
        'class_id',
        'stream_id',
        'admission_date',
        'profile_photo_path',
    ];

    /**
     * Get the full name of the student.
     */
    public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get the student code (using admission_no).
     */
    public function getStudentCodeAttribute()
    {
        return $this->admission_no;
    }

    /**
     * Get the class name.
     */
    public function getClassAttribute()
    {
        $class = DB::table('classes')->where('id', $this->class_id)->first();
        return $class ? $class->name : null;
    }

    /**
     * Get the stream name.
     */
    public function getStreamAttribute()
    {
        $stream = DB::table('streams')->where('id', $this->stream_id)->first();
        return $stream ? $stream->name : null;
    }

    /**
     * Get the guardian phone.
     */
    public function getPhoneAttribute()
    {
        $guardian = DB::table('guardians')->where('id', $this->guardian_id)->first();
        return $guardian ? $guardian->phone : null;
    }

    /**
     * Get admission date (using admission_date field, fallback to created_at).
     */
    public function getAdmissionDateFormattedAttribute()
    {
        if ($this->admission_date) {
            return $this->admission_date;
        }
        return $this->created_at ? $this->created_at->format('Y-m-d') : null;
    }
}
