<?php

namespace App\Models\Parto\Hotel;

use App\Models\HotelBookedRoom;
use App\Models\Order;
use App\Models\User;
use App\Parto\Enums\HotelQueueStatus;
use App\Traits\HasMetaAttribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class HotelBooking extends Model
{
    use HasMetaAttribute, HasUlids;
    
    protected $fillable = ['hotel_id', 'parto_unique_id', 'supplier', 'vat_number', 'payment_valid_until', 'payment_time_extendable', 'status'];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payment_valid_until' => 'datetime',
            'status' => HotelQueueStatus::class
        ];
    }

    public function order()
    {
        return $this->morphOne(Order::class, 'purchasable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rooms()
    {
        return $this->hasMany(HotelBookedRoom::class);
    }

    public function getUri()
    {
        return route('user.bookings.hotel.show', ['airBooking' => $this->id]);
    }
}
