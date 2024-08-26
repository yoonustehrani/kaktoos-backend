<?php

use App\Events\OrderPaid;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketController;
use App\Models\Order;
use App\Payment\PaymentGateway;
use Illuminate\Http\Request;
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