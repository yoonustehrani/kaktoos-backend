<?php

use App\Attributes\Description;
use App\Events\OrderPaid;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketController;
use App\Models\Order;
use App\Parto\Domains\Flight\Enums\AirBook\AirBookCategory;
use App\Parto\Domains\Flight\Enums\TravellerGender;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
use App\Parto\Domains\Flight\Enums\TravellerSeatPreference;
use App\Parto\Domains\Flight\FlightBook\AirTraveler;
use App\Parto\Parto;
use App\Payment\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;


Route::get('orders/{order}', function(Order $order, Request $request) {
    $order->load('purchasable');
    return $order;
});

Route::get('orders/{order}/events', function (Order $order) {
    OrderPaid::dispatch($order);
    return [
        'okay' => true
    ];
});

Route::get('orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');
Route::get('tickets/{ticketId}', [TicketController::class, 'show'])->name('tickets.show');


Route::get('/pay', function() {
    /**
     * @var \App\Payment\PaymentGateway
     */
    $purchase = app()->make(PaymentGateway::getGatewayClassname('jibit'));
    // $order->title
    $purchase->gateway->setRequestItem('description', 'پرداخت برای تست');
    $purchase->init(amount: 1000, ref: \Str::random(18));
    if ($purchase->requestPurchase()) {
        // $order->gateway_purchase_id = $purchase->getPurchaseId();
        // $order->save();
        return redirect()->to($purchase->getRedirectUrl());
    }
});

Route::view('/', 'welcome');

// Route::get('/', function () {
//     // return Parto::orderTicket('PO0068353');
//     return Parto::getBookingDetails('PO0068354');
//     // return dd(
//     //     // Parto::cancel('PO0068285')
//     //     // Parto::getBookingDetails('PO0068285')
//     //     // 
//     // );
//     $t1 = AirTraveler::make()
//         ->setName(firstName: 'Yoonus', middleName: '', lastName: 'Tehrani')
//         ->setGender(TravellerGender::tryFrom('male'))
//         ->setBirthdate(Carbon::createFromFormat('Y-m-d','2003-04-17'))
//         ->setNationality('IR')
//         ->setPassengerType(TravellerPassengerType::tryFrom('adult'))
//         ->setNationalId('0926534831')
//         ->setSeatPreference(TravellerSeatPreference::tryFrom('any'));
//     // $t2 = AirTraveler::make()
//     //     ->setName(firstName: 'Mohammad', middleName: '', lastName: 'Tehrani')
//     //     ->setGender(TravellerGender::tryFrom('male'))
//     //     ->setBirthdate(Carbon::createFromFormat('Y-m-d','1970-04-17'))
//     //     ->setNationality('IR')
//     //     ->setPassengerType(TravellerPassengerType::tryFrom('adult'))
//     //     ->setNationalId('0933090544')
//     //     ->setSeatPreference(TravellerSeatPreference::tryFrom('any'));

//     $airBook = Parto::flight()->flightBook()
//         ->setFareCode('363130666639353030366230343738656262343338316362323836366261373626323037322635363839363537')
//         ->addTraveler($t1)
//         // ->addTraveler($t2)
//         ->setPhoneNumber('09150013422')
//         ->setEmail('yoonustehrani28@gmail.com');
//     return Parto::flightBook($airBook);
//     // return view('welcome');
// });