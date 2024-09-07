<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Payment\PaymentGateway;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function pay(Order $order)
    {
        Gate::authorize('update', $order);

        try {
            DB::beginTransaction();
            /**
             * @var \App\Payment\PaymentGateway
             */
            $purchase = app()->make(PaymentGateway::getGatewayClassname('jibit'));
            // $order->title
            $purchase->gateway->setRequestItem('description', $order->title);
            $trx = new Transaction();
            $order->transactions()->save($trx);
            $purchase->init(amount: $order->amount, ref: $trx->id);
            if ($purchase->requestPurchase()) {
                $order->gateway_purchase_id = $purchase->getPurchaseId();
                $order->save();
                DB::commit();
                return redirect()->to($purchase->getRedirectUrl());
            }
            throw new Exception('Failed to file payment request');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}