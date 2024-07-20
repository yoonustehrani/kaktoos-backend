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
    }
}