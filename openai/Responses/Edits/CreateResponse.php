<?php

namespace OpenAI\Responses\Edits;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{object, created: int, choices<int, array{text, index: int}>, usage{prompt_tokens: int, completion_tokens: int, total_tokens: int}}>
 */
final class CreateResponse implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{id, object, created: int, model, choices<int, array{text, index: int, logprobs: int|null, finish_reason}>, usage{prompt_tokens: int, completion_tokens: int, total_tokens: int}}>
     */
    use ArrayAccessible;

    use Fakeable;

	private $object;
	private $created;
	private $choices;
	private $usage;
    /**
     * @param  array<int, CreateResponseChoice>  $choices
     */
    private function __construct(
        string $object,
        int $created,
        array $choices,
        CreateResponseUsage $usage
    ) {
        $this->object = $object;
        $this->created = $created;
        $this->choices = $choices;
        $this->usage = $usage;
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{object, created: int, choices<int, array{text, index: int}>, usage{prompt_tokens: int, completion_tokens: int, total_tokens: int}}  $attributes
     */
    public static function from(array $attributes)
    {
        $choices = array_map(fn (array $result) => CreateResponseChoice::from(
            $result
        ), $attributes['choices']);

        return new self(
            $attributes['object'],
            $attributes['created'],
            $choices,
            CreateResponseUsage::from($attributes['usage'])
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return [
            'object' => $this->object,
            'created' => $this->created,
            'choices' => array_map(
                static fn (CreateResponseChoice $result) => $result->toArray(),
                $this->choices,
            ),
            'usage' => $this->usage->toArray(),
        ];
    }
}
