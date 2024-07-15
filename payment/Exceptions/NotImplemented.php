<?php

namespace Payment\Exceptions;

use Exception;

class NotImplemented extends Exception
{
    public function __construct($destiny)
    {
        $message = 'Payment method not implemented on ' . $destiny;
        parent::__construct($message);
    }
}