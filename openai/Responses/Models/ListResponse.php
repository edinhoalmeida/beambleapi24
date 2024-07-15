<?php

namespace OpenAI\Responses\Models;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{object, data<int, array{id, object, created: int, owned_by, permission<int, array{id, object, created: int, allow_create_engine, allow_sampling, allow_logprobs, allow_search_indices, allow_view, allow_fine_tuning, organization, group: ?string, is_blocking}>, root, parent: ?string}>}>
 */
final class ListResponse implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{object, data<int, array{id, object, created: int, owned_by, permission<int, array{id, object, created: int, allow_create_engine, allow_sampling, allow_logprobs, allow_search_indices, allow_view, allow_fine_tuning, organization, group: ?string, is_blocking}>, root, parent: ?string}>}>
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
     * @param  array{object, data<int, array{id, object, created: int, owned_by, permission<int, array{id, object, created: int, allow_create_engine, allow_sampling, allow_logprobs, allow_search_indices, allow_view, allow_fine_tuning, organization, group: ?string, is_blocking}>, root, parent: ?string}>}  $attributes
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
