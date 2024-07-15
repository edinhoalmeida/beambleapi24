<?php

namespace OpenAI\Testing\Resources;

use OpenAI\Contracts\Resources\ImagesContract;
use OpenAI\Resources\Images;
use OpenAI\Responses\Images\CreateResponse;
use OpenAI\Responses\Images\EditResponse;
use OpenAI\Responses\Images\VariationResponse;
use OpenAI\Testing\Resources\Concerns\Testable;

final class ImagesTestResource implements ImagesContract
{
    use Testable;

    protected function resource()
    {
        return Images::class;
    }

    public function create(array $parameters)
    {
        return $this->record(__FUNCTION__, $parameters);
    }

    public function edit(array $parameters)
    {
        return $this->record(__FUNCTION__, $parameters);
    }

    public function variation(array $parameters)
    {
        return $this->record(__FUNCTION__, $parameters);
    }
}
