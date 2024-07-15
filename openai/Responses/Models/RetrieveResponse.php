<?php

namespace OpenAI\Responses\Models;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{id, object, created: int, owned_by, permission<int, array{id, object, created: int, allow_create_engine, allow_sampling, allow_logprobs, allow_search_indices, allow_view, allow_fine_tuning, organization, group: ?string, is_blocking}>, root, parent: ?string}>
 */
final class RetrieveResponse implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{id, object, created: int, owned_by, permission<int, array{id, object, created: int, allow_create_engine, allow_sampling, allow_logprobs, allow_search_indices, allow_view, allow_fine_tuning, organization, group: ?string, is_blocking}>, root, parent: ?string}>
     */
    use ArrayAccessible;

    use Fakeable;

    private $id;
    private $object;
    private $created;
    private $ownedBy;
    private $permission;
    private $root;
    private $parent;

    /**
     * @param  array<int, RetrieveResponsePermission>  $permission
     */
    private function __construct(
        string $id,
        string $object,
        int $created,
        string $ownedBy,
        array $permission,
        string $root,
        ?string $parent
    ) {

        $this->id = $id;
        $this->object = $object;
        $this->created = $created;
        $this->ownedBy = $ownedBy;
        $this->permission = $permission;
        $this->root = $root;
        $this->parent = $parent;

    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{id, object, created: int, owned_by, permission<int, array{id, object, created: int, allow_create_engine, allow_sampling, allow_logprobs, allow_search_indices, allow_view, allow_fine_tuning, organization, group: ?string, is_blocking}>, root, parent: ?string}  $attributes
     */
    public static function from(array $attributes)
    {
        $permission = array_map(fn (array $result) => RetrieveResponsePermission::from(
            $result
        ), $attributes['permission']);

        return new self(
            $attributes['id'],
            $attributes['object'],
            $attributes['created'],
            $attributes['owned_by'],
            $permission,
            $attributes['root'],
            $attributes['parent'],
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
            'created' => $this->created,
            'owned_by' => $this->ownedBy,
            'permission' => array_map(
                static fn (RetrieveResponsePermission $result) => $result->toArray(),
                $this->permission,
            ),
            'root' => $this->root,
            'parent' => $this->parent,
        ];
    }
}
