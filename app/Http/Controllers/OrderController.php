<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Events\OrderPaid;
use App\Http\Resources\OrderCollection;
use App\Models\AirBooking;
use App\Models\Order;
use App\Models\Parto\Hotel\HotelBooking;
use App\Models\Transaction;
use App\Parto\Domains\Flight\PricedItinerary;
use App\Parto\Facades\Parto;
use App\Payment\PaymentGateway;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    /**
     * Lists all of user's orders paginated
     */
    public function index()
    {
        return response()->json(
            new OrderCollection(
                get_auth_user()->orders()->paginate(10)
            )
        );
    }

    public function pay(Order $order, Request $request)
    {
        Gate::authorize('update', $order);
        if ($order->purchasable_type == AirBooking::class) {
            $airBooking = $order->purchasable;
            $live_price = get_flight_total_price($airBooking);
            if ($order->amount != $live_price) {
                $order->update([
                    'amount' => $live_price
                ]);
                return view('orders.confirm', compact('order'));
            }
        }
        if ($request->has('credit')) {
            return $this->creditPayment($order);
        }
        return $this->gatewayPayment($order);
    }

    protected function creditPayment(Order $order)
    {
        if ($order->amount_to_be_paid > get_auth_user()->credit) {
            abort(403, __('Insufficient credit'));
        }
        get_auth_user()->decreaseCredit($order->amount_to_be_paid);
        OrderPaid::dispatch($order);
        return redirect()->to(get_order_final_url($order));
    }

    protected function gatewayPayment(Order $order)
    {
        try {
            DB::beginTransaction();
            /**
             * @var \App\Payment\PaymentGateway
             */
            $purchase = app()->make(PaymentGateway::getGatewayClassname('jibit'));
            // $order->title
            $purchase->gateway->setRequestItem('description', $order->title);
            $purchase->gateway->setRequestItem('userIdentifier', (string) Auth::id());
            $purchase->gateway->setRequestItem('payerMobileNumber', get_auth_user()->phone_number);
            $trx = new Transaction([
                'status' => TransactionStatus::AWAITING,
                'amount' => $order->amount_to_be_paid,
            ]);
            $order->transactions()->save($trx);
            $purchase->init(amount: $order->amount_to_be_paid, ref: $trx->id);
            if ($purchase->requestPurchase()) {
                $trx->update([
                    'gateway_purchase_id' => $purchase->getPurchaseId()
                ]);
                DB::commit();
                return redirect()->to($purchase->getRedirectUrl());
            }
            DB::rollBack();
            throw new Exception('Failed to file payment request');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}