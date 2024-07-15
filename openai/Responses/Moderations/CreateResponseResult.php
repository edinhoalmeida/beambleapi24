<?php

namespace OpenAI\Responses\Moderations;

use OpenAI\Enums\Moderations\Category;

final class CreateResponseResult
{

	private $categories;
	private $flagged;

    /**
     * @param  array<string, CreateResponseCategory>  $categories
     */
    private function __construct(
        array $categories,
        bool $flagged
    ) {
		$this->categories = $categories;
		$this->flagged = $flagged;
    }


    /**
     * @param  array{categories<string, bool>, category_scores<string, float>, flagged}  $attributes
     */
    public static function from(array $attributes)
    {
        /** @var array<string, CreateResponseCategory> $categories */
        $categories = [];

        $cat_constantes  = Categories::getConstants();

        foreach ($cat_constantes as $category) {
            $categories[$category] = CreateResponseCategory::from([
                'category' => $category,
                'violated' => $attributes['categories'][$category],
                'score' => $attributes['category_scores'][$category],
            ]);
        }

        return new CreateResponseResult(
            $categories,
            $attributes['flagged']
        );
    }

    /**
     * @return array{categories<string, bool>, category_scores<string, float>, flagged}
     */
    public function toArray()
    {
        $categories = [];
        $categoryScores = [];
        foreach ($this->categories as $category) {
            $categories[$category->category] = $category->violated;
            $categoryScores[$category->category] = $category->score;
        }

        return [
            'categories' => $categories,
            'category_scores' => $categoryScores,
            'flagged' => $this->flagged,
        ];
    }
}
