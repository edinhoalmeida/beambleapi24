<?php

namespace OpenAI\Responses\Moderations;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{id, model, results<int, array{categories<string, bool>, category_scores<string, float>, flagged}>}>
 */
final class CreateResponse implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{id, model, results<int, array{categories<string, bool>, category_scores<string, float>, flagged}>}>
     */
    use ArrayAccessible;

    use Fakeable;

    private $id;
    private $model;
    private $results;

    /**
     * @param  array<int, CreateResponseResult>  $results
     */
    private function __construct(
        string $id,
        string $model,
        array $results
    ) {
        
        $this->id = $id;
        $this->model = $model;
        $this->results = $results;

    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{id, model, results<int, array{categories<string, bool>, category_scores<string, float>, flagged}>}  $attributes
     */
    public static function from(array $attributes)
    {
        $results = array_map(fn (array $result) => CreateResponseResult::from(
            $result
        ), $attributes['results']);

        return new self(
            $attributes['id'],
            $attributes['model'],
            $results,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'model' => $this->model,
            'results' => array_map(
                static fn (CreateResponseResult $result) => $result->toArray(),
                $this->results,
            ),
        ];
    }
}
