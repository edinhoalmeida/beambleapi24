<?php

namespace OpenAI\Responses\Files;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{object, data<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>}>
 */
final class ListResponse implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{object, data<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>}>
     */
    use ArrayAccessible;

    use Fakeable;

	private $object;
	private $data;
    /**
     * @param  array<int, RetrieveResponse>  $data
     */
    private function __construct(
        string $object,
        array $data
    ) {
        $this->object = $object;
        $this->data = $data;
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{object, data<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>}  $attributes
     */
    public static function from(array $attributes)
    {
        $data = array_map(fn (array $result) => RetrieveResponse::from(
            $result
        ), $attributes['data']);

        return new self(
            $attributes['object'],
            $data,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return [
            'object' => $this->object,
            'data' => array_map(
                static fn (RetrieveResponse $response) => $response->toArray(),
                $this->data,
            ),
        ];
    }
}
