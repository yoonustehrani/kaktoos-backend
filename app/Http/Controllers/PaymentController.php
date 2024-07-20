<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function verify(Request $request, string $gateway)
    {
        // TODO: verify the purchase
        $order = Order::find($request->input('clientReferenceNumber'));
        OrderPaid::dispatch($order);
        $url = preg_replace('/^([a-z]{1,}\.)(.+$)/i', '${2}', $request->host());
        $url .= '/flight/final?url=' . urlencode($order->purchasable->getUri());
        return redirect()->to($url);
    }
}