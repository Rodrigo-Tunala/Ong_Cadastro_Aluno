<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name',
        'birth_date',
        'shift',
        'phone',
        'address',
        'neighborhood',
        'school',
        'school_situation',
        'school_year',
        'responsible_id',
    ];

    public function responsible()
    {
        return $this->belongsTo(Responsible::class);
    }
    public function measurements()
    {
        return $this->hasMany(StudentMeasurement::class);
    }
}
