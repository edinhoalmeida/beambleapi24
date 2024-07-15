<?php

namespace OpenAI\Resources;

use OpenAI\Contracts\Resources\ImagesContract;
use OpenAI\Responses\Images\CreateResponse;
use OpenAI\Responses\Images\EditResponse;
use OpenAI\Responses\Images\VariationResponse;
use OpenAI\ValueObjects\Transporter\Payload;

final class Images implements ImagesContract
{
    use Concerns\Transportable;

    /**
     * Creates an image given a prompt.
     *
     * @see https://beta.openai.com/docs/api-reference/images/create
     *
     * @param  array<string, mixed>  $parameters
     */
    public function create(array $parameters)
    {
        $payload = Payload::create('images/generations', $parameters);

        /** @var array{created: int, data<int, array{url?, b64_json?}>} $result */
        $result = $this->transporter->requestObject($payload);

        return CreateResponse::from($result);
    }

    /**
     * Creates an edited or extended image given an original image and a prompt.
     *
     * @see https://beta.openai.com/docs/api-reference/images/create-edit
     *
     * @param  array<string, mixed>  $parameters
     */
    public function edit(array $parameters)
    {
        $payload = Payload::upload('images/edits', $parameters);

        /** @var array{created: int, data<int, array{url?, b64_json?}>} $result */
        $result = $this->transporter->requestObject($payload);

        return EditResponse::from($result);
    }

    /**
     * Creates a variation of a given image.
     *
     * @see https://beta.openai.com/docs/api-reference/images/create-variation
     *
     * @param  array<string, mixed>  $parameters
     */
    public function variation(array $parameters)
    {
        $payload = Payload::upload('images/variations', $parameters);

        /** @var array{created: int, data<int, array{url?, b64_json?}>} $result */
        $result = $this->transporter->requestObject($payload);

        return VariationResponse::from($result);
    }
}
