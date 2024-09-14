<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Hotel;
use App\Models\Order;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Process;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(CountrySeeder::class);
        // $this->call(StateSeeder::class);
        // $this->call(CitySeeder::class);
        // $this->call(AccommodationTypeSeeder::class);
        // $this->call(HotelSeeder::class);

        // Process::run('php artisan scout:flush "' . Hotel::class . '"')->throw();
        // Process::run('php artisan scout:flush "' . City::class . '"')->throw();

        // Process::run('php artisan scout:sync-index-settings');

        // Process::run('php artisan scout:import "' . Hotel::class . '"')->throw();
        // Process::run('php artisan scout:import "' . City::class . '"')->throw();

        // $this->call(AirportSeeder::class);
        // $this->call(AirlineSeeder::class);

        $user = User::factory()->state([
            'phone_number' => '09121234567',
            'email' => 'yoonustehrani@example.com'
        ])->create();

        $user->orders()->save(Order::factory()->make());
    }
}
