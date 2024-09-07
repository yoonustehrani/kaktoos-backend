<?php

namespace Database\Seeders;

use App\Models\Hotel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use JsonMachine\Items;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $files = array_filter(scandir(database_path('/seeders/data/international-hotels')), fn($f) => str_starts_with($f, 'Property_'));
        natsort($files);
        $matches = null;
        preg_match('/Property_\d{1,}\-(\d{1,})/', last($files), $matches);
        // $total_number = intval($matches[1]);
        $this->command->info('Reading Hotels From Files');
        foreach ($files as $file) {
            $path_to_file = database_path('/seeders/data/international-hotels/' . $file);
            $fileSize = filesize($path_to_file);
            $matches = null;
            preg_match('/Property_\d{1,}\-(\d{1,})/', $file, $matches);
            [$all, $to] = $matches;
            $items = collect([]);
            $hotels = Items::fromFile($path_to_file);
            foreach ($hotels as $hotel) {
                $items->push([
                    'id' => $hotel->Id,
                    'name' => $hotel->Name,
                    'city_id' => $hotel->PropertyCityId,
                    'rating' => $hotel->Rating ?? null,
                    'score' => $hotel->ReviewScore ?? null,
                    'address' => $hotel->Address ?? null,
                    'phone' => $hotel->Phone ?? null,
                    'email' => $hotel->Email ?? null,
                    'url' => $hotel->Url ?? null,
                    'lat' => $hotel->Latitude ?? null,
                    'lon' => $hotel->Longitude ?? null,
                    'accommodation_type_id' => $hotel->Accommodation ?? null
                ]);
            }
            $this->command->newLine();
            $this->command->info('Inserting Hotels into DB: up to ' . $to);
            $batches = $items->chunk(100);
            $bar = $this->command->getOutput()->createProgressBar($items->count());
            $bar->start();
            $batches->each(function($batch) use($bar) {
                Hotel::insert($batch->toArray());
                $bar->advance($batch->count());
            });
            $bar->finish();
            break;
        }
    }
}
