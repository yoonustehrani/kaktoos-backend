<?php

use App\Events\OrderPaid;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketController;
use App\Models\Order;
use App\Parto\Enums\HotelQueueStatus;
use App\Payment\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;


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
    Route::get('tickets/{ticketId}', [TicketController::class, 'show'])->name('tickets.show');
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