<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    protected $fillable = [];
    public $timestamps = false;
    protected $appends = ['google_maps_url'];

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

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }
    
    public function getGoogleMapsUrlAttribute()
    {
        return "https://www.google.com/maps/search/?api=1&query={$this->latitude}%2C{$this->longitude}";
    }
}
