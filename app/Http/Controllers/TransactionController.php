<?php

namespace App\Http\Controllers;

use App\Attributes\DisplayFa;
use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = get_auth_user()->transactions();
        return response()->json([
            'data' => $transactions->simplePaginate(20),
            'meta' => [
                'status' => TransactionStatus::describe()
            ]
        ]);
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('order.purchasable');
        return $transaction;
    }
}
