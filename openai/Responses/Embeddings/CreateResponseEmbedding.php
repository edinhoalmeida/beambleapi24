<?php

namespace OpenAI\Responses\Embeddings;

final class CreateResponseEmbedding
{
	private $object;
	private $index;
	private $embedding;
    /**
     * @param  array<int, float>  $embedding
     */
    private function __construct(
        string $object,
        int $index,
        array $embedding
    ) {
        $this->object = $object;
        $this->index = $index;
        $this->embedding = $embedding;
    }

    /**
     * @param  array{object, index: int, embedding<int, float>}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            $attributes['object'],
            $attributes['index'],
            $attributes['embedding'],
        );
    }

    /**
     * @return array{object, index: int, embedding<int, float>}
     */
    public function toArray()
    {
        return [
            'object' => $this->object,
            'index' => $this->index,
            'embedding' => $this->embedding,
        ];
    }
}
