<?php

namespace OpenAI\Responses\FineTunes;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{object, data<int, array{object, created_at: int, level, message}>}>
 */
final class ListEventsResponse implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{object, data<int, array{object, created_at: int, level, message}>}>
     */
    use ArrayAccessible;

    use Fakeable;

	private $object;
	private $data;
    /**
     * @param  array<int, RetrieveResponseEvent>  $data
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
     * @param  array{object, data<int, array{object, created_at: int, level, message}>}  $attributes
     */
    public static function from(array $attributes)
    {
        $data = array_map(fn (array $result) => RetrieveResponseEvent::from(
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
                static fn (RetrieveResponseEvent $response) => $response->toArray(),
                $this->data,
            ),
        ];
    }
}
