<?php
// baseado no repo: https://github.com/openai-php/client
// 

namespace OpenAI;

use OpenAI\Client;
use OpenAI\Factory;

final class OpenAI
{
    /**
     * Creates a new Open AI Client with the given API token.
     */
    public static function client(string $apiKey, string $organization = null)
    {
        return self::factory()
            ->withApiKey($apiKey)
            ->withOrganization($organization)
            ->make();
    }

    /**
     * Creates a new factory instance to configure a custom Open AI Client
     */
    public static function factory()
    {
        return new Factory();
    }
}
