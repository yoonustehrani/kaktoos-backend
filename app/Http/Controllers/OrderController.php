<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Events\OrderPaid;
use App\Http\Resources\OrderCollection;
use App\Models\AirBooking;
use App\Models\Order;
use App\Models\Parto\Hotel\HotelBooking;
use App\Models\Transaction;
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
        // Revalidate once more here
        // Move this to a seperated function
        if ($request->has('credit')) {
            abort_if($order->amount > get_auth_user()->credit, 403, __('Insufficient credit'));
            $url = str_replace('api.', '', config('app.url'));
            switch ($order->purchasable_type) {
                case AirBooking::class:
                    $url .= '/flight/final';
                    break;
                case HotelBooking::class:
                    $url .= '/hotel/final';
                    break;
                default:
                    throw new Exception('Purchasable type not supported!');
            }
            $url .= '?url=' . urlencode($order->purchasable->getUri());
            OrderPaid::dispatch($order);
            return redirect()->to($url);
        }
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
                'amount' => $order->amount,
            ]);
            $order->transactions()->save($trx);
            $purchase->init(amount: $order->amount, ref: $trx->id);
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