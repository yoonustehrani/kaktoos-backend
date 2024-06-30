<?php

namespace App\Http\Controllers;

use App\Models\PhoneAuthCode;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class UserAuthController extends Controller
{
    protected $basic_login_validation;

    public function __construct()
    {
        $this->basic_login_validation = [
            'phone_number' => ['required', 'string', 'regex:'. config('auth.regex.iranian_phone_number')]
        ];
    }
    protected function sendAuthCode(Request $request)
    {
        $request->validate($this->basic_login_validation);
        [$phone_number, $ip] = [$request->input('phone_number'), $request->ip()];

        /**
         * @var PhoneAuthCode|null
         */
        $record = PhoneAuthCode::where('phone_number', $phone_number)->where('ip', $ip)->first();

        /**
         * @var int system attempt rates for successfully sent SMS
         */
        $sms_rate = intval(Cache::remember("sms:$ip:$phone_number", now()->addHour(), fn() => 0));
        if ($sms_rate > 5) {
            return response()->json([
                'message' => 'No more tries! try after ' . now()->diffForHumans($record->created_at->addHour(1), CarbonInterface::DIFF_ABSOLUTE)
            ], 429);
        }

        
        $code = PhoneAuthCode::getNewCodeFor($phone_number, $ip);
        
        if (config('services.sms.enabled')) {
            $sms_sent = app('sms')->to($request->input('phone_number'))->sendPattern('login', [$code]);
        } else {
            \Log::info("code: $code");
            $sms_sent = true;
        }

        /**
         * Checking if SMS containing the $code is sent to $phone number
         * Incrementing the number of system attempt rates for successfully sent SMS
         * Returning successful status
         */
        if ($sms_sent) {
            Cache::increment("sms:$ip:$phone_number");
            return [
                'okay' => true,
                'message' => 'SMS sent successfully',
                'rate' => $sms_rate + 1
            ];
        }

        /**
         * SMS was not sent so we inform user
         */
        return response()->json([
            'okay' => false,
            'message' => 'Couldn\'t send SMS'
        ], 500);
    }
    public function login(Request $request)
    {
        // $request->session()->regenerate();
        // return $request->user('web');
        if ($request->user('web')) {
            return response()->json([
                'message' => 'Already logged in'
            ], 403);
        }
        /**
         * No code field is provided so the user is asking for sms codes
         */
        if ($request->missing('code')) {
            $rlKey = 'sms:' . $request->ip();
            if (RateLimiter::tooManyAttempts($rlKey, $perTwoMinutes = 1, $decayRate = 120)) {
                return response()->json([
                    'message' => 'Too many requests! you may request again in ' . RateLimiter::availableIn($rlKey) . ' seconds'
                ], 429);
            }
            RateLimiter::increment($rlKey);
            return $this->sendAuthCode($request);
        }

        /**
         * Attempting to check the auth code
         */
        $request->validate(array_merge([ 'code' => 'required|integer|digits:5' ], $this->basic_login_validation));
        $record = PhoneAuthCode::where('phone_number', $request->input('phone_number'))->where('ip', $request->ip())->first();

        if (! $record) {
            return response()->json([
                'message' => 'No sms record for this phone number',
                'errors' => [
                    'phone_number' => [
                        'No sms record for this phone number'
                    ]
                ]
            ], 419);
        }

        if (Hash::check($request->input('code'), $record->code)) {
            $user = User::wherePhoneNumber($request->input('phone_number'))->first();
            if (! $user) {
                $user = new User([
                    'phone_number' => $request->input('phone_number'),
                    'password' => 'must-change'
                ]);
                $user->save();
            }
            Auth::login($user);
            // $request->session()->regenerate();
            return response()->json([
                'okay' => true,
                'message' => 'Login successful',
                'user' => $user
            ]);
        }

        return response()->json([
            'message' => 'Incorrect code',
            'errors' => [
                'code' => [
                    'The entered code is incorrect'
                ]
            ]
        ], 419);
    }
}
