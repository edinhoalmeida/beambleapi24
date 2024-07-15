<?php

namespace OpenAI\Responses\Chat;

final class CreateResponseChoice
{

    private $index;
    private $message;
    private $finishReason;

    private function __construct(
        int $index,
        CreateResponseMessage $message,
        ?string $finishReason
    ) {

        $this->index = $index;
        $this->message = $message;
        $this->finishReason = $finishReason;

    }

    /**
     * @param  array{index: int, message{role, content}, finish_reason|null}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            $attributes['index'],
            CreateResponseMessage::from($attributes['message']),
            $attributes['finish_reason'],
        );
    }

    /**
     * @return array{index: int, message{role, content}, finish_reason|null}
     */
    public function toArray()
    {
        return [
            'index' => $this->index,
            'message' => $this->message->toArray(),
            'finish_reason' => $this->finishReason,
        ];
    }
}
