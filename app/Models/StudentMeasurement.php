<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentMeasurement extends Model
{
    protected $fillable = [
        'shirt_size',
        'shorts_size',
        'shoe_size',
        'student_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
