<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Laravel\Scout\Searchable;

class City extends Model
{
    use Searchable;

    public $timestamps = false;
    protected $hidden = ['state_id'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        return Arr::dot($array);
    }

    public function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with('state.country');
    }
}
