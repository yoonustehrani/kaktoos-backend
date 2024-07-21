<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Payment\PaymentGateway;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function pay(Order $order)
    {
        Gate::authorize('update', $order);

        /**
         * @var \App\Payment\PaymentGateway
         */
        $purchase = app()->make(PaymentGateway::getGatewayClassname('jibit'));
        // $order->title
        $purchase->gateway->setRequestItem('description', 'پرداخت برای تست');
        $purchase->init(amount: $order->amount, ref: $order->id);
        if ($purchase->requestPurchase()) {
            $order->gateway_purchase_id = $purchase->getPurchaseId();
            $order->save();
            return redirect()->to($purchase->getRedirectUrl());
        }
    }
}