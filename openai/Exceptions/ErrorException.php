<?php

namespace OpenAI\Exceptions;

use Exception;

final class ErrorException extends Exception
{
    /**
     * Creates a new Exception instance.
     *
     * @param  array{message, type, code}  $contents
     */
    public function __construct(array $contents)
    {
        parent::__construct($contents['message']);
    }

    /**
     * Returns the error message.
     */
    public function getErrorMessage()
    {
        return $this->getMessage();
    }

    /**
     * Returns the error type.
     */
    public function getErrorType()
    {
        return $this->contents['type'];
    }

    /**
     * Returns the error type.
     */
    public function getErrorCode()
    {
        return $this->contents['code'];
    }
}
