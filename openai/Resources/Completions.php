<?php

namespace OpenAI\Resources;

use OpenAI\Contracts\Resources\CompletionsContract;
use OpenAI\Responses\Completions\CreateResponse;
use OpenAI\Responses\Completions\CreateStreamedResponse;
use OpenAI\Responses\StreamResponse;
use OpenAI\ValueObjects\Transporter\Payload;

final class Completions implements CompletionsContract
{
    use Concerns\Transportable;
    use Concerns\Streamable;

    /**
     * Creates a completion for the provided prompt and parameters
     *
     * @see https://beta.openai.com/docs/api-reference/completions/create-completion
     *
     * @param  array<string, mixed>  $parameters
     */
    public function create(array $parameters)
    {
        $this->ensureNotStreamed($parameters);

        $payload = Payload::create('completions', $parameters);

        /** @var array{id, object, created: int, model, choices<int, array{text, index: int, logprobs{tokens<int, string>, token_logprobs<int, float>, top_logprobs<int, string>|null, text_offset<int, int>}|null, finish_reason}>, usage{prompt_tokens: int, completion_tokens: int, total_tokens: int}} $result */
        $result = $this->transporter->requestObject($payload);

        return CreateResponse::from($result);
    }

    /**
     * Creates a streamed completion for the provided prompt and parameters
     *
     * @see https://beta.openai.com/docs/api-reference/completions/create-completion
     *
     * @param  array<string, mixed>  $parameters
     * @return StreamResponse<CreateStreamedResponse>
     */
    public function createStreamed(array $parameters)
    {
        $parameters = $this->setStreamParameter($parameters);

        $payload = Payload::create('completions', $parameters);

        $response = $this->transporter->requestStream($payload);

        return new StreamResponse(CreateStreamedResponse::class, $response);
    }
}
