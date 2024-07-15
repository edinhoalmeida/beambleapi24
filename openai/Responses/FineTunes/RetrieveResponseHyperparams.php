<?php

namespace OpenAI\Responses\FineTunes;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;

/**
 * @implements ResponseContract<array{batch_size: ?int, learning_rate_multiplier: ?float, n_epochs: int, prompt_loss_weight: float}>
 */
final class RetrieveResponseHyperparams implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{batch_size: ?int, learning_rate_multiplier: ?float, n_epochs: int, prompt_loss_weight: float}>
     */
    use ArrayAccessible;

	private $batchSize;
	private $learningRateMultiplier;
	private $nEpochs;
	private $promptLossWeight;

    private function __construct(
        ?int $batchSize,
        ?float $learningRateMultiplier,
        int $nEpochs,
        float $promptLossWeight
    ) {
        $this->batchSize = $batchSize;
        $this->learningRateMultiplier = $learningRateMultiplier;
        $this->nEpochs = $nEpochs;
        $this->promptLossWeight = $promptLossWeight;
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{batch_size: ?int, learning_rate_multiplier: ?float, n_epochs: int, prompt_loss_weight: float}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            $attributes['batch_size'],
            $attributes['learning_rate_multiplier'],
            $attributes['n_epochs'],
            $attributes['prompt_loss_weight'],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return [
            'batch_size' => $this->batchSize,
            'learning_rate_multiplier' => $this->learningRateMultiplier,
            'n_epochs' => $this->nEpochs,
            'prompt_loss_weight' => $this->promptLossWeight,
        ];
    }
}
