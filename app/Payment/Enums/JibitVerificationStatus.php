<?php

namespace App\Payment\Enums;

use App\Attributes\Description;
use App\Traits\EnumAttributeCatcher;

enum JibitVerificationStatus
{
    use EnumAttributeCatcher;

    #[Description('The verification was successful.')]
    case SUCCESSFUL;

    #[Description('The verification failed. The payment amount will return to the user bank account.')]
    case FAILED;

    #[Description('Because of a fraud in payment from user, the payment reversed. The payment amount will return to the user bank account. ')]
    case REVERSED;

    #[Description('The verification was unknown (typically because of a network failure). The client could retry the verification for this purchase before its expiration. The client also could inquiry the purchase later to identify the actual payment verification state.')]
    case UNKNOWN;

    #[Description('The purchase already verified. For example a purchase with SUCCESS state is already verified.')]
    case ALREADY_VERIFIED;

    #[Description('The purchase is not prepare to verify and thus is not verifiable. Only purchases in READY_TO_VERIFY state can be verified.')]
    case NOT_VERIFIABLE;
}