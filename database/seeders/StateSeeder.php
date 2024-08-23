<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use JsonMachine\Items;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path_to_file = database_path('/seeders/data/international-hotels/States.json');
        $fileSize = filesize($path_to_file);
        $states = Items::fromFile($path_to_file, ['debug' => true]);
        $this->command->info('Reading States.json');
        $bar = $this->command->getOutput()->createProgressBar($fileSize);
        $bar->start();
        $items = collect([]);
        foreach ($states as $state) {
            $bar->setProgress($states->getPosition());
            $items->push([
                'id' => $state->Id,
                'name' => $state->Name,
                'country_code' => $state->CountryId,
            ]);
        }
        $bar->finish();
        $bar->clear();
        $this->command->info('Inserting States into DB');
        $batches = $items->chunk(1000);
        $bar = $this->command->getOutput()->createProgressBar($items->count());
        $batches->each(function($batch) use($bar) {
            $bar->advance($batch->count());
            State::insert($batch->toArray());
        });
        $bar->finish();
        $this->command->newLine();
    }
}
