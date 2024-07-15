<?php

namespace OpenAI\Responses\Edits;

final class CreateResponseChoice
{
	private $text;
	private $index;
    
    private function __construct(
        string $text,
        int $index
    ) {
        $this->text = $text;
        $this->index = $index;
    }

    /**
     * @param  array{text, index: int}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            $attributes['text'],
            $attributes['index'],
        );
    }

    /**
     * @return array{text, index: int}
     */
    public function toArray()
    {
        return [
            'text' => $this->text,
            'index' => $this->index,
        ];
    }
}
