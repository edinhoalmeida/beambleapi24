<?php

namespace OpenAI\Responses\Completions;

final class CreateResponseChoice
{

    public $text;
    private $index;
    private $logprobs;
    private $finishReason;


    private function __construct(
        string $text,
        int $index,
        ?CreateResponseChoiceLogprobs $logprobs,
        ?string $finishReason
    ) {
        $this->text = $text;
        $this->index = $index;
        $this->logprobs = $logprobs;
        $this->finishReason = $finishReason;
    }

    /**
     * @param  array{text, index: int, logprobs{tokens<int, string>, token_logprobs<int, float>, top_logprobs<int, string>|null, text_offset<int, int>}|null, finish_reason|null}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            $attributes['text'],
            $attributes['index'],
            $attributes['logprobs'] ? CreateResponseChoiceLogprobs::from($attributes['logprobs']) : null,
            $attributes['finish_reason'],
        );
    }

    /**
     * @return array{text, index: int, logprobs{tokens<int, string>, token_logprobs<int, float>, top_logprobs<int, string>|null, text_offset<int, int>}|null, finish_reason|null}
     */
    public function toArray()
    {
        return [
            'text' => $this->text,
            'index' => $this->index,
            'logprobs' => $this->logprobs !== null ? $this->logprobs->toArray() : null,
            'finish_reason' => $this->finishReason,
        ];
    }
}
