<?php

namespace OpenAI\Resources;

use OpenAI\Contracts\Resources\ModelsContract;
use OpenAI\Responses\Models\DeleteResponse;
use OpenAI\Responses\Models\ListResponse;
use OpenAI\Responses\Models\RetrieveResponse;
use OpenAI\ValueObjects\Transporter\Payload;

final class Models implements ModelsContract
{
    use Concerns\Transportable;

    /**
     * Lists the currently available models, and provides basic information about each one such as the owner and availability.
     *
     * @see https://beta.openai.com/docs/api-reference/models/list
     */
    public function list()
    {
        $payload = Payload::list('models');

        /** @var array{object, data<int, array{id, object, created: int, owned_by, permission<int, array{id, object, created: int, allow_create_engine, allow_sampling, allow_logprobs, allow_search_indices, allow_view, allow_fine_tuning, organization, group: ?string, is_blocking}>, root, parent: ?string}>} $result */
        $result = $this->transporter->requestObject($payload);

        return ListResponse::from($result);
    }

    /**
     * Retrieves a model instance, providing basic information about the model such as the owner and permissioning.
     *
     * @see https://beta.openai.com/docs/api-reference/models/retrieve
     */
    public function retrieve(string $model)
    {
        $payload = Payload::retrieve('models', $model);

        /** @var array{id, object, created: int, owned_by, permission<int, array{id, object, created: int, allow_create_engine, allow_sampling, allow_logprobs, allow_search_indices, allow_view, allow_fine_tuning, organization, group: ?string, is_blocking}>, root, parent: ?string} $result */
        $result = $this->transporter->requestObject($payload);

        return RetrieveResponse::from($result);
    }

    /**
     * Delete a fine-tuned model. You must have the Owner role in your organization.
     *
     * @see https://beta.openai.com/docs/api-reference/fine-tunes/delete-model
     */
    public function delete(string $model)
    {
        $payload = Payload::delete('models', $model);

        /** @var array{id, object, deleted} $result */
        $result = $this->transporter->requestObject($payload);

        return DeleteResponse::from($result);
    }
}
