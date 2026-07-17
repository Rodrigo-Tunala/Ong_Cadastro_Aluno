<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Responsible extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'phone',
        'cpf',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
        
}

