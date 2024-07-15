<?php

namespace Shipping\Exceptions;

use Exception;

class NotImplemented extends Exception
{
    public function __construct($destiny)
    {
        $message = 'Shipping method not implemented on ' . $destiny;
        parent::__construct($message);
    }
}