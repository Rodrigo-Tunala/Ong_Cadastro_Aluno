<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;
    
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
        
        public function studentMeasurement()
        {
            return $this->hasOne(StudentMeasurement::class);
    }

    
}
