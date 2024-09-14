<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Traits\HasMetaAttribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, HasUuids, HasMetaAttribute;

    protected $fillable = ['status', 'status_notes', 'payer_ip', 'amount', 'gateway_purchase_id', 'meta', 'paid_at', 'verified_at'];

    public function casts()
    {
        return [
            'status' => TransactionStatus::class,
            'paid_at' => 'datetime',
            'verified_at' => 'datetime'
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
