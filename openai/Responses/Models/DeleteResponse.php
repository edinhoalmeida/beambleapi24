<?php

namespace OpenAI\Responses\Models;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{id, object, deleted}>
 */
final class DeleteResponse implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{id, object, deleted}>
     */
    use ArrayAccessible;

    use Fakeable;

    private $id;
    private $object;
    private $deleted;

    private function __construct(
        string $id,
        string $object,
        bool $deleted
    ) {
        $this->id = $id;
        $this->object = $object;
        $this->deleted = $deleted;
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{id, object, deleted}  $attributes
     */
    public static function from(array $attributes)
    {
        return new self(
            $attributes['id'],
            $attributes['object'],
            $attributes['deleted'],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'object' => $this->object,
            'deleted' => $this->deleted,
        ];
    }
}
