<?php

namespace OpenAI\Testing\Resources;

use OpenAI\Contracts\Resources\ModelsContract;
use OpenAI\Resources\Models;
use OpenAI\Responses\Models\DeleteResponse;
use OpenAI\Responses\Models\ListResponse;
use OpenAI\Responses\Models\RetrieveResponse;
use OpenAI\Testing\Resources\Concerns\Testable;

final class ModelsTestResource implements ModelsContract
{
    use Testable;

    protected function resource()
    {
        return Models::class;
    }

    public function list()
    {
        return $this->record(__FUNCTION__);
    }

    public function retrieve(string $model)
    {
        return $this->record(__FUNCTION__, $model);
    }

    public function delete(string $model)
    {
        return $this->record(__FUNCTION__, $model);
    }
}
