<?php

namespace OpenAI\Responses\Images;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;

/**
 * @implements ResponseContract<array{url}|array{b64_json}>
 */
final class VariationResponseData implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{url}|array{b64_json}>
     */
    use ArrayAccessible;

    private  $url;
    private  $b64_json;

    private function __construct(
        string $url = '',
        string $b64_json = ''
    ) {
        $this->url = $url;
        $this->b64_json = $b64_json;
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{url?, b64_json?}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            $attributes['url'] ?? '',
            $attributes['b64_json'] ?? '',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->url !== '' && $this->url !== '0' ?
            ['url' => $this->url] :
            ['b64_json' => $this->b64_json];
    }
}
