<?php

namespace App\Http\Controllers;

use App\CreditAction;
use App\Models\CreditLog;
use Illuminate\Http\Request;

class UserCreditLogController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            array_merge(get_auth_user()->credit_logs()->paginate(10)->toArray(), [
                'meta' => [
                    'action' => CreditAction::describe()
                ]
            ])
        );
    }

    public function show(CreditLog $credit_log)
    {

    }
}
