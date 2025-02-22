<?php

namespace App\Http\Controllers;

use App\Attributes\DisplayFa;
use App\Enums\TransactionFailReason;
use App\Enums\TransactionStatus;
use App\Enums\VerificationResultStatus;
use App\Jobs\OrderTransactionPaid;
use App\Models\Transaction;
use App\Payment\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        $trx = Transaction::with('order.user')->findOrFail($request->input('clientReferenceNumber'));
        if ($trx->status == TransactionStatus::AWAITING) {
            $trx->update([
                'status' => TransactionStatus::PROCESSING
            ]);
        }
        /**
         * @var \App\Payment\PaymentGateway
         */
        $payment = app()->make(PaymentGateway::getGatewayClassname('jibit'));
        if ($request->input('status') == 'SUCCESSFUL') {
            $url = get_order_final_url($trx->order);
            $verification = $payment->gateway->validatePayment($request);
            if (
                VerificationResultStatus::{$verification['status']} == VerificationResultStatus::SUCCESSFUL
                ||
                VerificationResultStatus::{$verification['status']} == VerificationResultStatus::ALREADY_VERIFIED
            ) {
                if (VerificationResultStatus::{$verification['status']} == VerificationResultStatus::SUCCESSFUL) {
                    DB::transaction(function() use($trx, $request) {
                        $trx->order->user?->increaseCredit($request->amount);
                        $trx->update([
                            'status' => TransactionStatus::SUCCESS,
                            'status_notes' => null,
                            'payer_ip' => $request->payerIp,
                            'meta' => [
                                'card_number' => $request->payerMaskedCardNumber,
                                'psp_name' => $request->pspName,
                                'psp_rrn' => $request->pspRRN,
                                'psp_ref' => $request->pspReferenceNumber,
                                'psp_name' => $request->pspName,
                                'card_number_hashed' => $request->pspHashedCardNumber
                            ]
                        ]);
                    });
                    OrderTransactionPaid::dispatchSync($trx);
                }
                if (is_null($trx->paid_at)) {
                    $gateway_order = collect($payment->gateway->getOrderById($trx->gateway_purchase_id)['elements'])->last();
                    $trx->update([
                        'paid_at' => Carbon::parse($gateway_order['billingDate'])->setTimezone('Asia/Tehran'),
                        'verified_at' => Carbon::parse($gateway_order['verifiedAt'])->setTimezone('Asia/Tehran')
                    ]);
                }
                return view('transaction.success', compact('url', 'trx', 'request'))
                    ->with('verification_status', VerificationResultStatus::{$verification['status']});
            } else {
                return "وضعیت تراکنش نامشخص. در صورت کسر مبلغ طی ۷۲ ساعت مبلغ پرداختی به حسابتان باز میگردد.";
                // return view('transaction.unknow', compact('url', 'trx'))->with('verification_status', VerificationResultStatus::{$verification['status']});
            }
        } else {
            $trx->update([
                'status' => TransactionStatus::FAIL,
                'status_notes' => TransactionFailReason::{$request->failReason}->getAttributeValue(DisplayFa::class),
                'payer_ip' => $request->payerIp
            ]);
            return view('transaction.fail', [
                'trx' => $trx,
                'request' => $request,
                'url' => route('orders.pay', ['order' => $trx->order_id]),
            ]);
        }
    }
}