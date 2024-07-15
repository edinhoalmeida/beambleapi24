<?php

namespace OpenAI\Responses\FineTunes;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;

/**
 * @implements ResponseContract<array{object, created_at: int, level, message}>
 */
final class RetrieveResponseEvent implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{object, created_at: int, level, message}>
     */
    use ArrayAccessible;
    
	private $object;
	private $createdAt;
	private $level;
	private $message;

    private function __construct(
        string $object,
        int $createdAt,
        string $level,
        string $message
    ) {
        $this->object = $object;
        $this->createdAt = $createdAt;
        $this->level = $level;
        $this->message = $message;
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{object, created_at: int, level, message}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            $attributes['object'],
            $attributes['created_at'],
            $attributes['level'],
            $attributes['message'],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return [
            'object' => $this->object,
            'created_at' => $this->createdAt,
            'level' => $this->level,
            'message' => $this->message,
        ];
    }
}
