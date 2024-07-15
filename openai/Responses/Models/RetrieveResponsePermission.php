<?php

namespace OpenAI\Responses\Models;

final class RetrieveResponsePermission
{

    private $id;
    private $object;
    private $created;
    private $allowCreateEngine;
    private $allowSampling;
    private $allowLogprobs;
    private $allowSearchIndices;
    private $allowView;
    private $allowFineTuning;
    private $organization;
    private $group;
    private $isBlocking;

    private function __construct(
        string $id,
        string $object,
        int $created,
        bool $allowCreateEngine,
        bool $allowSampling,
        bool $allowLogprobs,
        bool $allowSearchIndices,
        bool $allowView,
        bool $allowFineTuning,
        string $organization,
        ?string $group,
        bool $isBlocking
    ) {

        $this->id = $id;
        $this->object = $object;
        $this->created = $created;
        $this->allowCreateEngine = $allowCreateEngine;
        $this->allowSampling = $allowSampling;
        $this->allowLogprobs = $allowLogprobs;
        $this->allowSearchIndices = $allowSearchIndices;
        $this->allowView = $allowView;
        $this->allowFineTuning = $allowFineTuning;
        $this->organization = $organization;
        $this->group = $group;
        $this->isBlocking = $isBlocking;

    }

    /**
     * @param  array{id, object, created: int, allow_create_engine, allow_sampling, allow_logprobs, allow_search_indices, allow_view, allow_fine_tuning, organization, group: ?string, is_blocking}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            $attributes['id'],
            $attributes['object'],
            $attributes['created'],
            $attributes['allow_create_engine'],
            $attributes['allow_sampling'],
            $attributes['allow_logprobs'],
            $attributes['allow_search_indices'],
            $attributes['allow_view'],
            $attributes['allow_fine_tuning'],
            $attributes['organization'],
            $attributes['group'],
            $attributes['is_blocking'],
        );
    }

    /**
     * @return array{id, object, created: int, allow_create_engine, allow_sampling, allow_logprobs, allow_search_indices, allow_view, allow_fine_tuning, organization, group: ?string, is_blocking}
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'object' => $this->object,
            'created' => $this->created,
            'allow_create_engine' => $this->allowCreateEngine,
            'allow_sampling' => $this->allowSampling,
            'allow_logprobs' => $this->allowLogprobs,
            'allow_search_indices' => $this->allowSearchIndices,
            'allow_view' => $this->allowView,
            'allow_fine_tuning' => $this->allowFineTuning,
            'organization' => $this->organization,
            'group' => $this->group,
            'is_blocking' => $this->isBlocking,
        ];
    }
}
