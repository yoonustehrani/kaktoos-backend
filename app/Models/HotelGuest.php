<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelGuest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [ 'first_name', 'last_name', 'type', 'title', 'age', 'national_id', 'passport_number' ];
    
}
