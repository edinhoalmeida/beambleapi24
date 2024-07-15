<?php

namespace OpenAI\Testing\Resources;

use OpenAI\Contracts\Resources\EditsContract;
use OpenAI\Resources\Edits;
use OpenAI\Responses\Edits\CreateResponse;
use OpenAI\Testing\Resources\Concerns\Testable;

final class EditsTestResource implements EditsContract
{
    use Testable;

    protected function resource()
    {
        return Edits::class;
    }

    public function create(array $parameters)
    {
        return $this->record(__FUNCTION__, $parameters);
    }
}
