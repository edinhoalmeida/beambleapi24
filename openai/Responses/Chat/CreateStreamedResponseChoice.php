<?php

namespace OpenAI\Responses\Chat;

final class CreateStreamedResponseChoice
{

	private $index;
	private $delta;
	private $finishReason;

    private function __construct(
        int $index,
        CreateStreamedResponseDelta $delta,
        ?string $finishReason
    ) {
		$this->index = $index;
		$this->delta = $delta;
		$this->finishReason = $finishReason;
    }

    /**
     * @param  array{index: int, delta{role?, content?}, finish_reason|null}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            $attributes['index'],
            CreateStreamedResponseDelta::from($attributes['delta']),
            $attributes['finish_reason'],
        );
    }

    /**
     * @return array{index: int, delta{role?, content?}, finish_reason|null}
     */
    public function toArray()
    {
        return [
            'index' => $this->index,
            'delta' => $this->delta->toArray(),
            'finish_reason' => $this->finishReason,
        ];
    }
}
