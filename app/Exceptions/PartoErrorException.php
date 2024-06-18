<?php

namespace App\Exceptions;

use Exception;

class PartoErrorException extends Exception
{
    public function __construct(public array $error)
    {
        
    }

    public function getErrorObject()
    {
        return (object) $this->error;
    }
}
