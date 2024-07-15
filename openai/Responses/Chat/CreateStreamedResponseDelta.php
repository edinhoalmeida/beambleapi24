<?php

namespace OpenAI\Responses\Chat;

final class CreateStreamedResponseDelta
{

	private $role;
	private $content;

    private function __construct(
        ?string $role,
        ?string $content
    ) {
        $this->role = $role;
        $this->content = $content;
    }

    /**
     * @param  array{role?, content?}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            $attributes['role'] ?? null,
            $attributes['content'] ?? null,
        );
    }

    /**
     * @return array{role?, content?}
     */
    public function toArray()
    {
        return array_filter([
            'role' => $this->role,
            'content' => $this->content,
        ]);
    }
}
