<?php

namespace OpenAI\Responses\Chat;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Testing\Responses\Concerns\FakeableForStreamedResponse;

/**
 * @implements ResponseContract<array{id, object, created: int, model, choices<int, array{index: int, delta{role?, content?}, finish_reason|null}>}>
 */
final class CreateStreamedResponse implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{id, object, created: int, model, choices<int, array{index: int, delta{role?, content?}, finish_reason|null}>}>
     */
    use ArrayAccessible;

    use FakeableForStreamedResponse;


    private $id;
    private $object;
    private $created;
    private $model;
    private $choices;

    /**
     * @param  array<int, CreateStreamedResponseChoice>  $choices
     */
    private function __construct(
        string $id,
        string $object,
        int $created,
        string $model,
        array $choices
    ) {
        $this->id = $id;
        $this->object = $object;
        $this->created = $created;
        $this->model = $model;
        $this->choices = $choices;
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{id, object, created: int, model, choices<int, array{index: int, delta{role?, content?}, finish_reason|null}>}  $attributes
     */
    public static function from(array $attributes)
    {
        $choices = array_map(fn (array $result) => CreateStreamedResponseChoice::from(
            $result
        ), $attributes['choices']);

        return new self(
            $attributes['id'],
            $attributes['object'],
            $attributes['created'],
            $attributes['model'],
            $choices,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'object' => $this->object,
            'created' => $this->created,
            'model' => $this->model,
            'choices' => array_map(
                static fn (CreateStreamedResponseChoice $result) => $result->toArray(),
                $this->choices,
            ),
        ];
    }
}
