<?php

namespace OpenAI\ValueObjects;

use OpenAI\Contracts\StringableContract;

/**
 * @internal
 */
final class ApiKey implements StringableContract
{
    private $apiKey;
    
    private function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public static function from(string $apiKey)
    {
        return new self($apiKey);
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return $this->apiKey;
    }
}
