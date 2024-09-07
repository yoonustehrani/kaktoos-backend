<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use JsonMachine\Items;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path_to_file = database_path('/seeders/data/international-hotels/Cities.json');
        $fileSize = filesize($path_to_file);
        $cities = Items::fromFile($path_to_file, ['debug' => true]);
        $this->command->info('Reading Cities.json');
        $bar = $this->command->getOutput()->createProgressBar($fileSize);
        $bar->start();
        $items = collect([]);
        foreach ($cities as $city) {
            $bar->setProgress($cities->getPosition());
            $items->push([
                'id' => $city->Id,
                'name' => $city->Name,
                'state_id' => $city->PropertyDestinationId,
            ]);
        }
        $bar->finish();
        $bar->clear();
        $this->command->info('Inserting Cities into DB');
        $batches = $items->chunk(2000);
        $bar = $this->command->getOutput()->createProgressBar($items->count());
        $batches->each(function($batch) use($bar) {
            $bar->advance($batch->count());
            City::insert($batch->toArray());
        });
        $bar->finish();
        $this->command->newLine();
    }
}
