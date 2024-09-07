<?php

namespace Database\Seeders;

use App\Models\AccommodationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccommodationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path_to_file = database_path('/seeders/data/international-hotels/PropertyAccommodation.json');
        $types = json_decode(file_get_contents($path_to_file));
        $this->command->info('Importing Accommodation Types');
        $bar = $this->command->getOutput()->createProgressBar(count($types));
        $bar->start();
        $batch = collect($types)->map(function($type) use($bar) {
            $bar->advance();
            return [
                'id' => $type->Id,
                'name' => $type->Name,
                'name_fa' => $type->NameFa,
            ];
        });
        AccommodationType::insert($batch->toArray());
        $bar->finish();
        $this->command->newLine();
    }
}
