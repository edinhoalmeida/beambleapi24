<?php

namespace OpenAI\Testing\Resources;

use OpenAI\Contracts\Resources\FineTunesContract;
use OpenAI\Resources\FineTunes;
use OpenAI\Responses\FineTunes\ListEventsResponse;
use OpenAI\Responses\FineTunes\ListResponse;
use OpenAI\Responses\FineTunes\RetrieveResponse;
use OpenAI\Responses\StreamResponse;
use OpenAI\Testing\Resources\Concerns\Testable;

final class FineTunesTestResource implements FineTunesContract
{
    use Testable;

    protected function resource()
    {
        return FineTunes::class;
    }

    public function create(array $parameters)
    {
        return $this->record(__FUNCTION__, $parameters);
    }

    public function list()
    {
        return $this->record(__FUNCTION__);
    }

    public function retrieve(string $fineTuneId)
    {
        return $this->record(__FUNCTION__, $fineTuneId);
    }

    public function cancel(string $fineTuneId)
    {
        return $this->record(__FUNCTION__, $fineTuneId);
    }

    public function listEvents(string $fineTuneId)
    {
        return $this->record(__FUNCTION__, $fineTuneId);
    }

    public function listEventsStreamed(string $fineTuneId)
    {
        return $this->record(__FUNCTION__, $fineTuneId);
    }
}
