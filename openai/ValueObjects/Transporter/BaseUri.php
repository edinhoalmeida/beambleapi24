<?php

namespace OpenAI\ValueObjects\Transporter;

use OpenAI\Contracts\StringableContract;

/**
 * @internal
 */
final class BaseUri implements StringableContract
{

    private $baseUri;
    /**
     * Creates a new Base URI value object.
     */
    private function __construct(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    /**
     * Creates a new Base URI value object.
     */
    public static function from(string $baseUri)
    {
        return new self($baseUri);
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return "https://{$this->baseUri}/";
    }
}
