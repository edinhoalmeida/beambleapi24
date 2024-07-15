<?php

namespace OpenAI\ValueObjects\Transporter;

use OpenAI\Enums\Transporter\ContentType;
use OpenAI\ValueObjects\ApiKey;

/**
 * @internal
 */
final class Headers
{
    private $headers = [];
    /**
     * Creates a new Headers value object.
     *
     * @param  array<string, string>  $headers
     */
    private function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Creates a new Headers value object
     */
    public static function create()
    {
        return new self([]);
    }

    /**
     * Creates a new Headers value object with the given API token.
     */
    public static function withAuthorization(ApiKey $apiKey)
    {
        return new self([
            'Authorization' => "Bearer {$apiKey->toString()}",
        ]);
    }

    /**
     * Creates a new Headers value object, with the given content type, and the existing headers.
     */
    public function withContentType(string $contentType, string $suffix = '')
    {
        $this->headers['Content-Type'] = $contentType . $suffix;
        return new self($this->headers);
    }

    /**
     * Creates a new Headers value object, with the given organization, and the existing headers.
     */
    public function withOrganization(string $organization)
    {
        $this->headers['OpenAI-Organization'] = $organization;
        return new self($this->headers);
    }

    /**
     * Creates a new Headers value object, with the newly added header, and the existing headers.
     */
    public function withCustomHeader(string $name, string $value)
    {
        $this->headers[$name] = $value;
        return new self($this->headers);
    }

    /**
     * @return array<string, string> $headers
     */
    public function toArray()
    {
        return $this->headers;
    }
}
