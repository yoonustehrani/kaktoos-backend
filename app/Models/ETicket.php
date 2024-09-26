<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ETicket extends Model
{
    use HasFactory;

    protected $fillable = [ 'ticket_number', 'air_booking_id', 'status', 'refunded', 'total_refund', 'issued_at', 'airline_pnr'];

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime'
        ];
    }
}
