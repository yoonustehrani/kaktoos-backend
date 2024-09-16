<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'type', 'title', 'gender',
        'first_name', 'middle_name', 'last_name',
        'birthdate',
        'country_code',
        'national_id', 
        'passport_number', 'passport_expires_on', 'passport_issued_on', 'passport_country'
    ];

    public function getFullNameAttribute()
    {
        return collect([$this->title, $this->first_name, $this->middle_name, $this->last_name])->filter()->join(' ');
    }

    public function nationality()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    public function tickets()
    {
        return $this->hasMany(ETicket::class);
    }
}
