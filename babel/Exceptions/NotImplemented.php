<?php

namespace Babel\Exceptions;

use Exception;

class NotImplemented extends Exception
{
    public function __construct($destiny)
    {
        $message = 'Translation method not implemented on ' . $destiny;
        parent::__construct($message);
    }
}