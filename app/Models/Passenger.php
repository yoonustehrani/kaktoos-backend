<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory, HasUlids;

    public function getFullNameAttribute()
    {
        return collect([$this->first_name, $this->middle_name, $this->last_name])->filter()->join(' ');
    }

    public function nationality()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }
}
