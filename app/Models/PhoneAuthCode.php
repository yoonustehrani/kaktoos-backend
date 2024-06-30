<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class PhoneAuthCode extends Model
{
    use HasFactory;

    protected $fillable = ['phone_number', 'ip', 'code', 'attempts'];
    // protected $primaryKey = ['phone_number', 'ip'];
    // public $incrementing = false;

    /**
     * @return string a 5-digit auth code
     */
    public static function getNewCodeFor($phone_number, $ip): string
    {
        $code = strval(random_int(10000, 99999));

        /**
         * Updating or Creating a record in phone_auth_codes table
         * Storing $code for the requested $phone_number and $ip
         */
        PhoneAuthCode::upsert([
            array_merge(compact('phone_number', 'ip'), [
                'code' => Hash::make($code),
            ])
        ], uniqueBy: ['phone_number', 'ip'], update: ['code']);

        return $code;
    }
}
