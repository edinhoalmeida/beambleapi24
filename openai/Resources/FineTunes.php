<?php

namespace OpenAI\Resources;

use OpenAI\Contracts\Resources\FineTunesContract;
use OpenAI\Responses\FineTunes\ListEventsResponse;
use OpenAI\Responses\FineTunes\ListResponse;
use OpenAI\Responses\FineTunes\RetrieveResponse;
use OpenAI\Responses\FineTunes\RetrieveStreamedResponseEvent;
use OpenAI\Responses\StreamResponse;
use OpenAI\ValueObjects\Transporter\Payload;

final class FineTunes implements FineTunesContract
{
    use Concerns\Transportable;

    /**
     * Creates a job that fine-tunes a specified model from a given dataset.
     *
     * Response includes details of the enqueued job including job status and the name of the fine-tuned models once complete.
     *
     * @see https://beta.openai.com/docs/api-reference/fine-tunes/create
     *
     * @param  array<string, mixed>  $parameters
     */
    public function create(array $parameters)
    {
        $payload = Payload::create('fine-tunes', $parameters);

        /** @var array{id, object, model, created_at: int, events<int, array{object, created_at: int, level, message}>, fine_tuned_model: ?string, hyperparams{batch_size: ?int, learning_rate_multiplier: ?float, n_epochs: int, prompt_loss_weight: float}, organization_id, result_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, status, validation_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, training_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, updated_at: int}  $result */
        $result = $this->transporter->requestObject($payload);

        return RetrieveResponse::from($result);
    }

    /**
     * List your organization's fine-tuning jobs.
     *
     * @see https://beta.openai.com/docs/api-reference/fine-tunes/list
     */
    public function list()
    {
        $payload = Payload::list('fine-tunes');

        /** @var array{object, data<int, array{id, object, model, created_at: int, events<int, array{object, created_at: int, level, message}>, fine_tuned_model: ?string, hyperparams{batch_size: ?int, learning_rate_multiplier: ?float, n_epochs: int, prompt_loss_weight: float}, organization_id, result_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, status, validation_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, training_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, updated_at: int}>} $result */
        $result = $this->transporter->requestObject($payload);

        return ListResponse::from($result);
    }

    /**
     * Gets info about the fine-tune job.
     *
     * @see https://beta.openai.com/docs/api-reference/fine-tunes/list
     */
    public function retrieve(string $fineTuneId)
    {
        $payload = Payload::retrieve('fine-tunes', $fineTuneId);

        /** @var array{id, object, model, created_at: int, events<int, array{object, created_at: int, level, message}>, fine_tuned_model: ?string, hyperparams{batch_size: ?int, learning_rate_multiplier: ?float, n_epochs: int, prompt_loss_weight: float}, organization_id, result_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, status, validation_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, training_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, updated_at: int}  $result */
        $result = $this->transporter->requestObject($payload);

        return RetrieveResponse::from($result);
    }

    /**
     * Immediately cancel a fine-tune job.
     *
     * @see https://beta.openai.com/docs/api-reference/fine-tunes/cancel
     */
    public function cancel(string $fineTuneId)
    {
        $payload = Payload::cancel('fine-tunes', $fineTuneId);

        /** @var array{id, object, model, created_at: int, events<int, array{object, created_at: int, level, message}>, fine_tuned_model: ?string, hyperparams{batch_size: ?int, learning_rate_multiplier: ?float, n_epochs: int, prompt_loss_weight: float}, organization_id, result_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, status, validation_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, training_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, updated_at: int}  $result */
        $result = $this->transporter->requestObject($payload);

        return RetrieveResponse::from($result);
    }

    /**
     * Get fine-grained status updates for a fine-tune job.
     *
     * @see https://beta.openai.com/docs/api-reference/fine-tunes/events
     */
    public function listEvents(string $fineTuneId)
    {
        $payload = Payload::retrieve('fine-tunes', $fineTuneId, '/events');

        /** @var array{object, data<int, array{object, created_at: int, level, message}>} $result */
        $result = $this->transporter->requestObject($payload);

        return ListEventsResponse::from($result);
    }

    /**
     * Get streamed fine-grained status updates for a fine-tune job.
     *
     * @see https://beta.openai.com/docs/api-reference/fine-tunes/events
     *
     * @return StreamResponse<RetrieveStreamedResponseEvent>
     */
    public function listEventsStreamed(string $fineTuneId)
    {
        $payload = Payload::retrieve('fine-tunes', $fineTuneId, '/events?stream=true');

        $response = $this->transporter->requestStream($payload);

        return new StreamResponse(RetrieveStreamedResponseEvent::class, $response);
    }
}
