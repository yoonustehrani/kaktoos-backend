<?php

use App\Parto\Domains\Flight\Enums\TravellerGender;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
use App\Parto\Domains\Flight\Enums\TravellerSeatPreference;
use App\Parto\Domains\Flight\FlightBook\AirTraveler;
use App\Parto\Parto;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return dd(
        // Parto::cancel('PO0068261')
        // Parto::getBookingDetails('PO0068261')
    );
    $t1 = AirTraveler::make()
        ->setName(firstName: 'Yoonus', middleName: '', lastName: 'Tehrani')
        ->setGender(TravellerGender::tryFrom('male'))
        ->setBirthdate(Carbon::createFromFormat('Y-m-d','2003-04-17'))
        ->setNationality('IR')
        ->setPassengerType(TravellerPassengerType::tryFrom('adult'))
        ->setNationalId('0926534831')
        ->setSeatPreference(TravellerSeatPreference::tryFrom('any'));
    $t2 = AirTraveler::make()
        ->setName(firstName: 'Mohammad', middleName: '', lastName: 'Tehrani')
        ->setGender(TravellerGender::tryFrom('male'))
        ->setBirthdate(Carbon::createFromFormat('Y-m-d','1970-04-17'))
        ->setNationality('IR')
        ->setPassengerType(TravellerPassengerType::tryFrom('adult'))
        ->setNationalId('0933090544')
        ->setSeatPreference(TravellerSeatPreference::tryFrom('any'));

    $airBook = Parto::flight()->flightBook()
        ->setFareCode('6434316236313430373863343434366239306134376635356234326261633931263130392635363835313135')
        ->addTraveler($t1)
        // ->addTraveler($t2)
        ->setPhoneNumber('09150013422')
        ->setEmail('yoonustehrani28@gmail.com');
    return Parto::flightBook($airBook);
    // return view('welcome');
});