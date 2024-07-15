<?php

namespace OpenAI\Resources;

use OpenAI\Contracts\Resources\EditsContract;
use OpenAI\Responses\Edits\CreateResponse;
use OpenAI\ValueObjects\Transporter\Payload;

final class Edits implements EditsContract
{
    use Concerns\Transportable;

    /**
     * Creates a new edit for the provided input, instruction, and parameters.
     *
     * @see https://beta.openai.com/docs/api-reference/edits/create
     *
     * @param  array<string, mixed>  $parameters
     */
    public function create(array $parameters)
    {
        $payload = Payload::create('edits', $parameters);

        /** @var array{object, created: int, choices<int, array{text, index: int}>, usage{prompt_tokens: int, completion_tokens: int, total_tokens: int}} $result */
        $result = $this->transporter->requestObject($payload);

        return CreateResponse::from($result);
    }
}
