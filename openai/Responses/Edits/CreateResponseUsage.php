<?php

namespace OpenAI\Responses\Edits;

final class CreateResponseUsage
{
	private $promptTokens;
	private $completionTokens;
	private $totalTokens;

    private function __construct(
        int $promptTokens,
        int $completionTokens,
        int $totalTokens
    ) {
        $this->promptTokens = $promptTokens;
        $this->completionTokens = $completionTokens;
        $this->totalTokens = $totalTokens;
    }

    /**
     * @param  array{prompt_tokens: int, completion_tokens: int, total_tokens: int}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            $attributes['prompt_tokens'],
            $attributes['completion_tokens'],
            $attributes['total_tokens'],
        );
    }

    /**
     * @return array{prompt_tokens: int, completion_tokens: int, total_tokens: int}
     */
    public function toArray()
    {
        return [
            'prompt_tokens' => $this->promptTokens,
            'completion_tokens' => $this->completionTokens,
            'total_tokens' => $this->totalTokens,
        ];
    }
}
