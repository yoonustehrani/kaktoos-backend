<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\CreditAction;
use App\Models\Parto\Hotel\HotelBooking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'phone_number',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function airBookings()
    {
        return $this->hasMany(AirBooking::class);
    }

    public function hotelBookings()
    {
        return $this->hasMany(HotelBooking::class);
    }

    public function credit_logs()
    {
        return $this->hasMany(CreditLog::class);
    }

    public function increaseCredit(int $amount)
    {
        $this->increment('credit', $amount);
        $this->credit_logs()->save(new CreditLog([
            'amount' => abs($amount),
            'status' => CreditAction::Increase
        ]));
    }

    public function decreaseCredit(int $amount)
    {
        $this->decrement('credit', $amount);
        $this->credit_logs()->save(new CreditLog([
            'amount' => abs($amount) * -1,
            'status' => CreditAction::Decrease
        ]));
    }
}
