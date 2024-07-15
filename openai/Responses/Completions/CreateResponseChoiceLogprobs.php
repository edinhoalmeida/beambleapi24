<?php

namespace OpenAI\Responses\Completions;

final class CreateResponseChoiceLogprobs
{

    private $tokens;
    private $tokenLogprobs;
    private $topLogprobs;
    private $textOffset;

    /**
     * @param  array<int, string>  $tokens
     * @param  array<int, float>  $tokenLogprobs
     * @param  array<int, string>|null  $topLogprobs
     * @param  array<int, int>  $textOffset
     */
    private function __construct(
        array $tokens,
        array $tokenLogprobs,
        ?array $topLogprobs,
        array $textOffset
    ) {
        $this->tokens = $tokens;
        $this->tokenLogprobs = $tokenLogprobs;
        $this->topLogprobs = $topLogprobs;
        $this->textOffset = $textOffset;
    }

    /**
     * @param  array{tokens<int, string>, token_logprobs<int, float>, top_logprobs<int, string>|null, text_offset<int, int>}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            $attributes['tokens'],
            $attributes['token_logprobs'],
            $attributes['top_logprobs'],
            $attributes['text_offset'],
        );
    }

    /**
     * @return array{tokens<int, string>, token_logprobs<int, float>, top_logprobs<int, string>|null, text_offset<int, int>}
     */
    public function toArray()
    {
        return [
            'tokens' => $this->tokens,
            'token_logprobs' => $this->tokenLogprobs,
            'top_logprobs' => $this->topLogprobs,
            'text_offset' => $this->textOffset,
        ];
    }
}
