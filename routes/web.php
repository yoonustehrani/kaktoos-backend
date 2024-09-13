<?php

use App\Events\OrderPaid;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketController;
use App\Models\AirBooking;
use App\Models\Order;
use App\Parto\Enums\HotelQueueStatus;
use App\Payment\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;


Route::get('/ticket', function() {
    $airBooking = AirBooking::latest()->first();
    $airBooking->load(['passengers.tickets', 'flights' => function($query) {
        $query->with(['arrival_airport.country', 'departure_airport.country', 'marketing_airline', 'operating_airline']);
    }]);
    $airBooking->passengers->append('fullname')->makeHidden(['first_name', 'middle_name', 'last_name', 'title']);
    return view('pdfs.ticket2')
        ->with('passengers', $airBooking->passengers)
        ->with('flights', $airBooking->flights);
});

Route::get('/ticket/data', function() {
    $airBooking = AirBooking::latest()->first();
    $airBooking->load(['passengers.tickets', 'flights' => function($query) {
        $query->with(['arrival_airport.country', 'departure_airport.country', 'marketing_airline', 'operating_airline']);
    }]);
    $airBooking->passengers->append('fullname')->makeHidden(['first_name', 'middle_name', 'last_name', 'title']);
    return $airBooking;
});
// return view('pdfs.ticket', [
    // 'company' => [
    //     'name' => 'کاکتوس سیر توس',
    //     'logo' => '',
    //     'phone_number' => '05131234567'
    // ],
    // 'ticket' => [
    //     'number' => '123456'
    // ]
// ]);

Route::get('/test', function() {
    $order = Order::latest()->first();
    DB::transaction(function() use($order) {
        $order->user?->increaseCredit($order->amount);
    });
    // return $order->purchasable;
    OrderPaid::dispatch($order);
});

Route::middleware('auth:sanctum')->group(function() {
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
    Route::get('air/bookings/{airBooking}/ticket', [TicketController::class, 'index'])->name('bookings.air.tickets.index');
});


Route::get('/pay', function() {
    /**
     * @var \App\Payment\PaymentGateway
     */
    $purchase = app()->make(PaymentGateway::getGatewayClassname('jibit'));
    // $order->title
    $purchase->gateway->setRequestItem('description', 'پرداخت برای تست');
    $purchase->init(amount: 1000, ref: Str::random(18));
    if ($purchase->requestPurchase()) {
        // $order->gateway_purchase_id = $purchase->getPurchaseId();
        // $order->save();
        return redirect()->to($purchase->getRedirectUrl());
    }
});

Route::view('/', 'welcome');