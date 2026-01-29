<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'profile_photo_path',
    ];
}
