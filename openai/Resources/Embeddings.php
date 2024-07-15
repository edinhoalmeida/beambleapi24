<?php

namespace OpenAI\Resources;

use OpenAI\Contracts\Resources\EmbeddingsContract;
use OpenAI\Responses\Embeddings\CreateResponse;
use OpenAI\ValueObjects\Transporter\Payload;

final class Embeddings implements EmbeddingsContract
{
    use Concerns\Transportable;

    /**
     * Creates an embedding vector representing the input text.
     *
     * @see https://beta.openai.com/docs/api-reference/embeddings/create
     *
     * @param  array<string, mixed>  $parameters
     */
    public function create(array $parameters)
    {
        $payload = Payload::create('embeddings', $parameters);

        /** @var array{object, data<int, array{object, embedding<int, float>, index: int}>, usage{prompt_tokens: int, total_tokens: int}} $result */
        $result = $this->transporter->requestObject($payload);

        return CreateResponse::from($result);
    }
}
