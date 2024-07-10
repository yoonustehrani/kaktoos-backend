<?php

namespace Database\Seeders;

use App\Models\Airline;
use App\Traits\CSVReader;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AirlineSeeder extends Seeder
{
    use CSVReader;

    public function downloadLogos()
    {
        $parto_airlines = $this->read(database_path('/seeders/data/airlines-with-logos.csv'));
        $parto_airlines->chunk(100)->each(function($chunk) {
            [$f, $s] = [$chunk->first()['icao'], $chunk->last()['icao']];
            $this->command->alert("FROM $f TO $s");
            $chunk->each(function($airline) {
                $airline['logo'] = null;
                if ($airline['icao']) {
                    $sources = [
                        'https://raw.githubusercontent.com/Jxck-S/airline-logos/main/flightaware_logos/%s.png',
                        'https://raw.githubusercontent.com/Jxck-S/airline-logos/main/radarbox_logos/%s.png',
                        'https://raw.githubusercontent.com/Jxck-S/airline-logos/main/radarbox_banners/%s.png',
                        'https://raw.githubusercontent.com/Jxck-S/airline-logos/main/fr24_logos/%s.png',
                        'https://raw.githubusercontent.com/Jxck-S/airline-logos/main/avcodes_banners/%s.png',
                    ];
                    foreach ($sources as $source) {
                        try {
                            $http = Http::accept('image/png')->retry(3);
                            $url = sprintf($source, $airline['icao']);
                            $response = $http->get($url);
                            if ($response->status() == 404) {
                                continue;
                            }
                            $this->command->info("FOUND {$airline['icao']}");
                            $success = false;
                            while (! $success) {
                                $success = Storage::put('/public/images/airlines/' . $airline['icao'] . '.png', $response->body());
                            }
                            break;
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                    }
                }
            });
        });
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->downloadLogos();
        // return;

        $parto_airlines = $this->read(__DIR__ . '/data/airlines.csv');
        $airlines = $this->read(__DIR__ . '/data/airlines-full.csv')->filter(fn($x) => strlen($x['iata']) > 1)->keyBy('iata');
        $iranian = [
            'IRA' => 'ایران ایر',
            'IRB' => 'ایران ایر تور',
            'IRC' => 'آسمان',
            'IRZ' => 'ساها',
            'KIS' => 'کیش',
            'IRM' => 'ماهان',
            'CPN' => 'کاسپین',
            'IRU' => 'چابهار',
            'IZG' => 'زاگرس',
            'TBN' => 'تابان',
            'TBZ' => 'آتا',
            'MRJ' => 'معراج',
            'SHI' => 'سپهران',
            'QSM' => 'قشم',
            'PYA' => 'پویا',
            'PRS' => 'پارس',
            'KRU' => 'کارون',
            'VRH' => 'وارش',
            'FPI' => 'پرشیا',
            'SJT' => 'آسا جت',
            'INA' => 'ایر وان',
            'DZD' => 'یزد',
            'AXV' => 'آوا'
        ];
        $parto_airlines = $parto_airlines->map(function($airline) use($airlines, $iranian) {
            if (strlen($airline['iata']) == 3) {
                $airline['icao'] = $airline['iata'];
            } else {
                $airline['icao'] = $airlines->has($airline['iata']) ? $airlines->get($airline['iata'])['icao'] : null;
            }
            if (in_array($airline['icao'], array_keys($iranian))) {
                $airline['name_fa'] = $iranian[$airline['icao']];
            } else {
                $airline['name_fa'] = null;
            }
            return $airline;
        });

        $parto_airlines->chunk(100)->each(function($chunk) {
            $chunk->each(function($a) {
                $a['logo'] = $a['icao'] && Storage::exists("/public/images/airlines/{$a['icao']}.png") 
                ?  "storage/images/airlines/{$a['icao']}.png"
                : null;
                (new Airline([
                    'code' => $a['iata'],
                    'logo' => $a['logo'],
                    'icao' => $a['icao'],
                    'name' => $a['name'],
                    'name_fa' => $a['name_fa'] ? 'هواپیمایی'. ' ' . $a['name_fa'] : null
                ]))->save();
            });
        });
    }
}
