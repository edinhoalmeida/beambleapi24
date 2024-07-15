<?php

namespace OpenAI\Responses\Chat;

final class CreateResponseMessage
{

	private $role;
	private $content;

    private function __construct(
        string $role,
        string $content
    ) {

        $this->role = $role;
        $this->content = $content;

    }

    /**
     * @param  array{role, content}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            $attributes['role'],
            $attributes['content'],
        );
    }

    /**
     * @return array{role, content}
     */
    public function toArray()
    {
        return [
            'role' => $this->role,
            'content' => $this->content,
        ];
    }
}
