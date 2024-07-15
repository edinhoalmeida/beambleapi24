<?php

namespace OpenAI\Responses\Images;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{created: int, data<int, array{url?, b64_json?}>}>
 */
final class CreateResponse implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{created: int, data<int, array{url?, b64_json?}>}>
     */
    use ArrayAccessible;

    use Fakeable;

    private $created;
    private $data;

    /**
     * @param  array<int, CreateResponseData>  $data
     */
    private function __construct(
        int $created,
        array $data
    ) {
        $this->created = $created;
        $this->data = $data;
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{created: int, data<int, array{url?, b64_json?}>}  $attributes
     */
    public static function from(array $attributes)
    {
        $results = array_map(fn (array $result) => CreateResponseData::from(
            $result
        ), $attributes['data']);

        return new self(
            $attributes['created'],
            $results,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return [
            'created' => $this->created,
            'data' => array_map(
                static fn (CreateResponseData $result) => $result->toArray(),
                $this->data,
            ),
        ];
    }
}
