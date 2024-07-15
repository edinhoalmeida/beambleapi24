<?php

namespace OpenAI\ValueObjects;

use OpenAI\Contracts\StringableContract;

/**
 * @internal
 */
final class ResourceUri implements StringableContract
{
    private $uri;
    /**
     * Creates a new ResourceUri value object.
     */
    private function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    /**
     * Creates a new ResourceUri value object that creates the given resource.
     */
    public static function create(string $resource)
    {
        return new self($resource);
    }

    /**
     * Creates a new ResourceUri value object that uploads to the given resource.
     */
    public static function upload(string $resource)
    {
        return new self($resource);
    }

    /**
     * Creates a new ResourceUri value object that lists the given resource.
     */
    public static function list(string $resource)
    {
        return new self($resource);
    }

    /**
     * Creates a new ResourceUri value object that retrieves the given resource.
     */
    public static function retrieve(string $resource, string $id, string $suffix)
    {
        return new self("{$resource}/{$id}{$suffix}");
    }

    /**
     * Creates a new ResourceUri value object that retrieves the given resource content.
     */
    public static function retrieveContent(string $resource, string $id)
    {
        return new self("{$resource}/{$id}/content");
    }

    /**
     * Creates a new ResourceUri value object that cancels the given resource.
     */
    public static function cancel(string $resource, string $id)
    {
        return new self("{$resource}/{$id}/cancel");
    }

    /**
     * Creates a new ResourceUri value object that deletes the given resource.
     */
    public static function delete(string $resource, string $id)
    {
        return new self("{$resource}/{$id}");
    }

    /**
     * {@inheritDoc}
     */
    public function toString()
    {
        return $this->uri;
    }
}