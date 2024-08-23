<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Laravel\Scout\Searchable;

class Hotel extends Model
{
    use Searchable;
    
    protected $hidden = ['city_id'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        // $this->attributes['province'] = $this->relations['city']['province']->toArray();
        // $this->relations['city'] = Arr::except($this->relations['city'], ['province']);
        $array = $this->toArray();
        return Arr::dot(Arr::except($array, ['created_at', 'updated_at', 'phone', 'email', 'url', 'lat', 'lon']));
    }

    public function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with('city.state.country');
    }
}
