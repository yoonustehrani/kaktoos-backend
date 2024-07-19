<?php

namespace App\Models;

use App\Traits\HasMetaAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, HasMetaAttribute;

    protected $fillable = ['user_id', 'gateway_purchase_id', 'amount', 'meta'];

    public function purchasable()
    {
        return $this->morphTo();
    }
}
