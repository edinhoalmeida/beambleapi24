<?php

namespace OpenAI\Responses\Completions;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Testing\Responses\Concerns\FakeableForStreamedResponse;

/**
 * @implements ResponseContract<array{id, object, created: int, model, choices<int, array{text, index: int, logprobs{tokens<int, string>, token_logprobs<int, float>, top_logprobs<int, string>|null, text_offset<int, int>}|null, finish_reason|null}>}>
 */
final class CreateStreamedResponse implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{id, object, created: int, model, choices<int, array{text, index: int, logprobs{tokens<int, string>, token_logprobs<int, float>, top_logprobs<int, string>|null, text_offset<int, int>}|null, finish_reason|null}>}>
     */
    use ArrayAccessible;

    use FakeableForStreamedResponse;

	private $id;
	private $object;
	private $created;
	private $model;
	private $choices;
    /**
     * @param  array<int, CreateResponseChoice>  $choices
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
     * @param  array{id, object, created: int, model, choices<int, array{text, index: int, logprobs{tokens<int, string>, token_logprobs<int, float>, top_logprobs<int, string>|null, text_offset<int, int>}|null, finish_reason}>, usage?{prompt_tokens: int, completion_tokens: int|null, total_tokens: int}}  $attributes
     */
    public static function from(array $attributes)
    {
        $choices = array_map(fn (array $result) => CreateResponseChoice::from(
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
                static fn (CreateResponseChoice $result) => $result->toArray(),
                $this->choices,
            ),
        ];
    }
}
