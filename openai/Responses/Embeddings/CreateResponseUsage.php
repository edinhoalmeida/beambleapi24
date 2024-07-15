<?php

namespace OpenAI\Responses\Embeddings;

final class CreateResponseUsage
{
	private $promptTokens;
	private $totalTokens;

    private function __construct(
        int $promptTokens,
        int $totalTokens
    ) {
        $this->promptTokens = $promptTokens;
        $this->totalTokens = $totalTokens;
    }

    /**
     * @param  array{prompt_tokens: int, total_tokens: int}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            $attributes['prompt_tokens'],
            $attributes['total_tokens'],
        );
    }

    /**
     * @return array{prompt_tokens: int, total_tokens: int}
     */
    public function toArray()
    {
        return [
            'prompt_tokens' => $this->promptTokens,
            'total_tokens' => $this->totalTokens,
        ];
    }
}
