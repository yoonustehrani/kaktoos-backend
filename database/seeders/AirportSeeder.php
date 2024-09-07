<?php

namespace Database\Seeders;

use App\Models\Airport;
use App\Traits\CSVReader;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AirportSeeder extends Seeder
{
    use CSVReader;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $airports_json = file_get_contents( __DIR__ . '/data/AirportsFa.json');
        $airports_json = (array) json_decode($airports_json);
        $iata_codes = array_keys($airports_json); 
        // LocationFa
        for ($i=1; $i <= 10; $i++) { 
            $this->command->alert("processing $i.csv");
            $airports = $this->read(__DIR__ . "/data/airports/$i.csv");
            $count = $airports->count();
            $this->command->info("processing $count airports");
            $result = $airports->filter(fn($airport) => $airport['country_code'] != 'IL')->map(function($airport) use($airports_json, $iata_codes) {
                $airport['is_international'] = false;
                if (strlen($airport['country_code']) > 2) {
                    $airport['country_code'] = null;
                }
                if (in_array($airport['IATA_code'], $iata_codes)) {
                    $airport_fa = $airports_json[$airport['IATA_code']];
                    $airport['name_fa'] = $airport_fa->AirportNameFa;
                    $airport['is_international'] = \Str::contains($airport_fa->AirportNameEng, 'International') || \Str::contains($airport['name'], 'International');
                    $delimiter = '،';
                    $airport['city_name_fa'] =  explode($delimiter, $airport_fa->LocationFa)[0] ?? null;
                    // $airport['location'] = $airport_fa->LocationEng;
                    $airport['latitude'] = $airport_fa->Latitude ?: null;
                    $airport['longitude'] = $airport_fa->Longitude ?: null;
                } else {
                    $airport['name_fa'] = null;
                    $airport['is_international'] = \Str::contains($airport['name'], 'International');
                    // $airport['location_fa'] = null;
                    // $airport['location'] = null;
                    $airport['latitude'] = null;
                    $airport['longitude'] = null;
                    $airport['city_name_fa'] = null;
                }
                return $airport;
            });
            $result->chunk(200)->map(function($chunked_results, $key) {
                $k = $key + 1;
                $this->command->info("inserting 200 airports - part: $k");
                try {
                    Airport::insert($chunked_results->toArray());
                } catch (\Throwable $th) {
                    \Log::error("Dataabase error: " . $th->getMessage());
                    \Log::alert($chunked_results->toArray());
                }
            });
        }
        $rated = [
            'THR' => [
                'name' => 'تهران',
                'rating' => 2.0
            ],
            'MHD' => [
                'name' => 'مشهد',
                'rating' => 1.9
            ],
            'AWZ' => [
                'name' => 'اهواز',
                'rating' => 1.8
            ],
            'SYZ' => [
                'name' => 'شیراز',
                'rating' => 1.7
            ],
            'BND' => [
                'name' => 'بندر عباس',
                'rating' => 1.6
            ],
            'IFN' => [
                'name' => 'اصفهان',
                'rating' => 1.5
            ],
            'TBZ' => [
                'name' => 'تبریز',
                'rating' => 1.4
            ],
            'KIH' => [
                'name' => 'کیش',
                'rating' => 1.3
            ]
        ];
        foreach ($rated as $iata => $city) {
            Airport::where('IATA_code', $iata)->update([
                'rating' => $city['rating']
            ]);
        }
    }
}
