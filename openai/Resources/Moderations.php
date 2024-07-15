<?php

namespace OpenAI\Resources;

use OpenAI\Contracts\Resources\ModerationsContract;
use OpenAI\Responses\Moderations\CreateResponse;
use OpenAI\ValueObjects\Transporter\Payload;

final class Moderations implements ModerationsContract
{
    use Concerns\Transportable;

    /**
     * Classifies if text violates OpenAI's Content Policy.
     *
     * @see https://beta.openai.com/docs/api-reference/moderations/create
     *
     * @param  array<string, mixed>  $parameters
     */
    public function create(array $parameters)
    {
        $payload = Payload::create('moderations', $parameters);

        /** @var array{id, model, results<int, array{categories<string, bool>, category_scores<string, float>, flagged}>} $result */
        $result = $this->transporter->requestObject($payload);

        return CreateResponse::from($result);
    }
}
