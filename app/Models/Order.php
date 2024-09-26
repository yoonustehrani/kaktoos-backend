<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Traits\HasMetaAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, HasMetaAttribute;

    protected $fillable = ['user_id', 'gateway_purchase_id', 'amount', 'amount_paid', 'status', 'meta', 'title', 'paid_at'];

    public function casts()
    {
        return [
            'status' => OrderStatus::class
        ];
    }

    public function purchasable()
    {
        return $this->morphTo();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAmountToBePaidAttribute(): int
    {
        return $this->amount - $this->amount_paid;
    }
}
