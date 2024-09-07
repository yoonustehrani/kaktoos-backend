<?php

namespace App\Models;

use App\Traits\HasMetaAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelBookedRoom extends Model
{
    use HasFactory, HasMetaAttribute;

    public $timestamps = false;

    protected $fillable = [ 'room_archive_id', 'name', 'meal_type', 'meta' ];

    public function guests()
    {
        return $this->hasMany(HotelGuest::class);
    }
}
