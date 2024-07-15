<?php

namespace OpenAI\Responses\FineTunes;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{id, object, model, created_at: int, events<int, array{object, created_at: int, level, message}>, fine_tuned_model: ?string, hyperparams{batch_size: ?int, learning_rate_multiplier: ?float, n_epochs: int, prompt_loss_weight: float}, organization_id, result_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, status, validation_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, training_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, updated_at: int}>
 */
final class RetrieveResponse implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{id, object, model, created_at: int, events<int, array{object, created_at: int, level, message}>, fine_tuned_model: ?string, hyperparams{batch_size: ?int, learning_rate_multiplier: ?float, n_epochs: int, prompt_loss_weight: float}, organization_id, result_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, status, validation_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, training_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, updated_at: int}>
     */
    use ArrayAccessible;

    use Fakeable;

	private $id;
	private $object;
	private $model;
	private $createdAt;
	private $events;
	private $fineTunedModel;
	private $hyperparams;
	private $organizationId;
	private $resultFiles;
	private $status;
	private $validationFiles;
	private $trainingFiles;
	private $updatedAt;
    /**
     * @param  array<int, RetrieveResponseEvent>  $events
     * @param  array<int, RetrieveResponseFile>  $resultFiles
     * @param  array<int, RetrieveResponseFile>  $validationFiles
     * @param  array<int, RetrieveResponseFile>  $trainingFiles
     */
    private function __construct(
        string $id,
        string $object,
        string $model,
        int $createdAt,
        array $events,
        ?string $fineTunedModel,
        RetrieveResponseHyperparams $hyperparams,
        string $organizationId,
        array $resultFiles,
        string $status,
        array $validationFiles,
        array $trainingFiles,
        int $updatedAt
    ) {
        $this->id = $id;
        $this->object = $object;
        $this->model = $model;
        $this->createdAt = $createdAt;
        $this->events = $events;
        $this->fineTunedModel = $fineTunedModel;
        $this->hyperparams = $hyperparams;
        $this->organizationId = $organizationId;
        $this->resultFiles = $resultFiles;
        $this->status = $status;
        $this->validationFiles = $validationFiles;
        $this->trainingFiles = $trainingFiles;
        $this->updatedAt = $updatedAt;
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{id, object, model, created_at: int, events?<int, array{object, created_at: int, level, message}>, fine_tuned_model: ?string, hyperparams{batch_size: ?int, learning_rate_multiplier: ?float, n_epochs: int, prompt_loss_weight: float}, organization_id, result_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, status, validation_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, training_files<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>, updated_at: int}  $attributes
     */
    public static function from(array $attributes)
    {
        $events = array_map(fn (array $result) => RetrieveResponseEvent::from(
            $result
        ), $attributes['events'] ?? []);

        $resultFiles = array_map(fn (array $result) => RetrieveResponseFile::from(
            $result
        ), $attributes['result_files']);

        $validationFiles = array_map(fn (array $result) => RetrieveResponseFile::from(
            $result
        ), $attributes['validation_files']);

        $trainingFiles = array_map(fn (array $result) => RetrieveResponseFile::from(
            $result
        ), $attributes['training_files']);

        return new self(
            $attributes['id'],
            $attributes['object'],
            $attributes['model'],
            $attributes['created_at'],
            $events,
            $attributes['fine_tuned_model'],
            RetrieveResponseHyperparams::from($attributes['hyperparams']),
            $attributes['organization_id'],
            $resultFiles,
            $attributes['status'],
            $validationFiles,
            $trainingFiles,
            $attributes['updated_at'],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'object' => $this->object,
            'model' => $this->model,
            'created_at' => $this->createdAt,
            'events' => array_map(
                static fn (RetrieveResponseEvent $result) => $result->toArray(),
                $this->events
            ),
            'fine_tuned_model' => $this->fineTunedModel,
            'hyperparams' => $this->hyperparams->toArray(),
            'organization_id' => $this->organizationId,
            'result_files' => array_map(
                static fn (RetrieveResponseFile $result) => $result->toArray(),
                $this->resultFiles
            ),
            'status' => $this->status,
            'validation_files' => array_map(
                static fn (RetrieveResponseFile $result) => $result->toArray(),
                $this->validationFiles
            ),
            'training_files' => array_map(
                static fn (RetrieveResponseFile $result) => $result->toArray(),
                $this->trainingFiles
            ),
            'updated_at' => $this->updatedAt,
        ];
    }
}
