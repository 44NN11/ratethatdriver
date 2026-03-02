<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = ['registration_number'];

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}