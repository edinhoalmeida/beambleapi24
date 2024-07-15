<?php

namespace OpenAI\Responses\Embeddings;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{object, data<int, array{object, embedding<int, float>, index: int}>, usage{prompt_tokens: int, total_tokens: int}}>
 */
final class CreateResponse implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{object, data<int, array{object, embedding<int, float>, index: int}>, usage{prompt_tokens: int, total_tokens: int}}>
     */
    use ArrayAccessible;

    use Fakeable;

	private $object;
	private $embeddings;
	private $usage;
    /**
     * @param  array<int, CreateResponseEmbedding>  $embeddings
     */
    private function __construct(
        string $object,
        array $embeddings,
        CreateResponseUsage $usage
    ) {
        $this->object = $object;
        $this->embeddings = $embeddings;
        $this->usage = $usage;
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{object, data<int, array{object, embedding<int, float>, index: int}>, usage{prompt_tokens: int, total_tokens: int}}  $attributes
     */
    public static function from(array $attributes)
    {
        $embeddings = array_map(fn (array $result) => CreateResponseEmbedding::from(
            $result
        ), $attributes['data']);

        return new self(
            $attributes['object'],
            $embeddings,
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
            'data' => array_map(
                static fn (CreateResponseEmbedding $result) => $result->toArray(),
                $this->embeddings,
            ),
            'usage' => $this->usage->toArray(),
        ];
    }
}
