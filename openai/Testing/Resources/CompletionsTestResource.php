<?php

namespace OpenAI\Testing\Resources;

use OpenAI\Contracts\Resources\CompletionsContract;
use OpenAI\Resources\Completions;
use OpenAI\Responses\Completions\CreateResponse;
use OpenAI\Responses\StreamResponse;
use OpenAI\Testing\Resources\Concerns\Testable;

final class CompletionsTestResource implements CompletionsContract
{
    use Testable;

    protected function resource()
    {
        return Completions::class;
    }

    public function create(array $parameters)
    {
        return $this->record(__FUNCTION__, $parameters);
    }

    public function createStreamed(array $parameters)
    {
        return $this->record(__FUNCTION__, $parameters);
    }
}
