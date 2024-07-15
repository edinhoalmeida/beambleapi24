<?php

namespace OpenAI\Resources;

use OpenAI\Contracts\Resources\AudioContract;
use OpenAI\Responses\Audio\TranscriptionResponse;
use OpenAI\Responses\Audio\TranslationResponse;
use OpenAI\ValueObjects\Transporter\Payload;

final class Audio implements AudioContract
{
    use Concerns\Transportable;

    /**
     * Transcribes audio into the input language.
     *
     * @see https://platform.openai.com/docs/api-reference/audio/create
     *
     * @param  array<string, mixed>  $parameters
     */
    public function transcribe(array $parameters)
    {
        $payload = Payload::upload('audio/transcriptions', $parameters);

        /** @var array{task: ?string, language: ?string, duration: ?float, segments<int, array{id: int, seek: int, start: float, end: float, text, tokens<int, int>, temperature: float, avg_logprob: float, compression_ratio: float, no_speech_prob: float, transient}>, text}|string $result */
        $result = $this->transporter->requestObject($payload);

        return TranscriptionResponse::from($result);
    }

    /**
     * Translates audio into English.
     *
     * @see https://platform.openai.com/docs/api-reference/audio/create
     *
     * @param  array<string, mixed>  $parameters
     */
    public function translate(array $parameters)
    {
        $payload = Payload::upload('audio/translations', $parameters);

        /** @var array{task: ?string, language: ?string, duration: ?float, segments<int, array{id: int, seek: int, start: float, end: float, text, tokens<int, int>, temperature: float, avg_logprob: float, compression_ratio: float, no_speech_prob: float, transient}>, text}|string $result */
        $result = $this->transporter->requestObject($payload);

        return TranslationResponse::from($result);
    }
}