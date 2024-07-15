<?php

namespace OpenAI\Responses\Moderations;

use OpenAI\Enums\Moderations\Category;

final class CreateResponseCategory
{

	private $category;
	private $violated;
	private $score;

    private function __construct(
        Category $category,
        bool $violated,
        float $score
    ) {
		$this->category = $category;
		$this->violated = $violated;
		$this->score = $score;
    }

    /**
     * @param  array{category, violated, score: float}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            Category::from($attributes['category']),
            $attributes['violated'],
            $attributes['score'],
        );
    }
}
