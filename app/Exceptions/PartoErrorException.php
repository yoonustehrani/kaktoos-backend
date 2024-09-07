<?php

namespace App\Exceptions;

use Exception;

class PartoErrorException extends Exception
{
    public string $id;
    public function __construct(public array $error)
    {
        $this->id = $error['Id'];
        $this->message = __('parto.web_service_error') . ": ". (
            __('parto.errors')[$this->id] ?? $error['Message']
        );
    }

    public function getErrorObject()
    {
        return (object) $this->error;
    }
}
