<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    protected $fillable = [];
    public $timestamps = false;
    use HasFactory;

    public function scopeOnlyNational(Builder $query)
    {
        $query->where('is_international', false);
    }
    public function scopeOnlyInternational(Builder $query)
    {
        $query->where('is_international', true);
    }
    public function scopeOnlyIran(Builder $query)
    {
        $query->where('country_code', 'IR');
    }
}
