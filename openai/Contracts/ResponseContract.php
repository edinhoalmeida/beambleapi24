<?php

namespace OpenAI\Contracts;

use ArrayAccess;

/**
 * @template TArray of array
 *
 * @extends ArrayAccess<key-of<TArray>, value-of<TArray>>
 *
 * @internal
 */
interface ResponseContract extends ArrayAccess
{
    /**
     * Returns the array representation of the Response.
     *
     * @return TArray
     */
    public function toArray();

    /**
     * @param  key-of<TArray>  $offset
     */
    public function offsetExists($offset);

    /**
     * @template TOffsetKey of key-of<TArray>
     *
     * @param  TOffsetKey  $offset
     * @return TArray[TOffsetKey]
     */
    public function offsetGet($offset);

    /**
     * @template TOffsetKey of key-of<TArray>
     *
     * @param  TOffsetKey  $offset
     * @param  TArray[TOffsetKey] $value
     */
    public function offsetSet($offset, $value);

    /**
     * @template TOffsetKey of key-of<TArray>
     *
     * @param  TOffsetKey  $offset
     */
    public function offsetUnset($offset);
}
