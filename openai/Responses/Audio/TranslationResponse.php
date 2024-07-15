<?php

namespace OpenAI\Responses\Audio;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{task: ?string, language: ?string, duration: ?float, segments<int, array{id: int, seek: int, start: float, end: float, text, tokens<int, int>, temperature: float, avg_logprob: float, compression_ratio: float, no_speech_prob: float, transient}>, text}>
 */
final class TranslationResponse implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{task: ?string, language: ?string, duration: ?float, segments<int, array{id: int, seek: int, start: float, end: float, text, tokens<int, int>, temperature: float, avg_logprob: float, compression_ratio: float, no_speech_prob: float, transient}>, text}>
     */
    use ArrayAccessible;

    use Fakeable;

	private $task;
	private $language;
	private $duration;
	private $segments;
	private $text;

    /**
     * @param  array<int, TranslationResponseSegment>  $segments
     */
    private function __construct(
        ?string $task,
        ?string $language,
        ?float $duration,
        array $segments,
        string $text
    ) {

        $this->task = $task;
        $this->language = $language;
        $this->duration = $duration;
        $this->segments = $segments;
        $this->text = $text;
     
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{task: ?string, language: ?string, duration: ?float, segments<int, array{id: int, seek: int, start: float, end: float, text, tokens<int, int>, temperature: float, avg_logprob: float, compression_ratio: float, no_speech_prob: float, transient}>, text}  $attributes
     */
    public static function from(array|string $attributes)
    {
        if (is_string($attributes)) {
            $attributes = ['text' => $attributes];
        }

        $segments = isset($attributes['segments']) ? array_map(fn (array $result) => TranslationResponseSegment::from(
            $result
        ), $attributes['segments']) : [];

        return new self(
            $attributes['task'] ?? null,
            $attributes['language'] ?? null,
            $attributes['duration'] ?? null,
            $segments,
            $attributes['text'],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return [
            'task' => $this->task,
            'language' => $this->language,
            'duration' => $this->duration,
            'segments' => array_map(
                static fn (TranslationResponseSegment $result) => $result->toArray(),
                $this->segments,
            ),
            'text' => $this->text,
        ];
    }
}
