<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Traits\CSVReader;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    use CSVReader;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = $this->read(__DIR__ . "/data/countries.csv")->filter(fn($x) => $x['code'] !== 'IL');
        Country::insert($countries->toArray());
    }
}
