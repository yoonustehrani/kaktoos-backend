<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\Transaction;
use App\Payment\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function verify(Request $request, string $gateway)
    {
        $request->validate([
            'amount' => 'required',
            'purchaseId' => 'required',
            'status' => 'required',
            'clientReferenceNumber' => 'required'
        ]);
        // /**
        //  * @var \App\Payment\PaymentGateway
        //  */
        // $payment = app()->make(PaymentGateway::getGatewayClassname('jibit'));
        // if ($request->input('status') == 'SUCCESSFUL') {
        //     // $result = $payment->gateway->getOrderById($request->input('purchaseId'));
        //     // return response()->streamDownload(function () use($request, $result) {
        //     //     echo json_encode($result, JSON_PRETTY_PRINT);
        //     // }, 'get-order.json', [
        //     //     'Content-Type' => 'application/json'
        //     // ]);
        //     $verification = $payment->gateway->validatePayment($request);
        //     if ($verification['status'] == 'SUCCESSFUL') {
        //         # payment is valid
        //     }
        // } else {
        //     dd(
        //         $payment->gateway->validatePayment($request)
        //     );
        //     dd($request->all());
        // }
        // TODO: verify the purchase
        /**
         * @var \App\Models\Order
         */
        $order = Transaction::findOrFail($request->input('clientReferenceNumber'))->order()->firstOrFail();
        DB::transaction(function() use($order) {
            $order->user?->increaseCredit($order->amount);
        });
        OrderPaid::dispatch($order);
        $url = preg_replace('/^([a-z]{1,}\.)(.+$)/i', '${2}', $request->host());
        $url .= '/flight/final?url=' . urlencode($order->purchasable->getUri());
        return redirect()->to($url);
    }
}