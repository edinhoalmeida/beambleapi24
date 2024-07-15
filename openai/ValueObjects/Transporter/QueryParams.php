<?php

namespace OpenAI\ValueObjects\Transporter;

/**
 * @internal
 */
final class QueryParams
{
    private $params = [];
    /**
     * Creates a new Query Params value object.
     *
     * @param  array<string, string>  $params
     */
    private function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Creates a new Query Params value object
     */
    public static function create()
    {
        return new self([]);
    }

    /**
     * Creates a new Query Params value object, with the newly added param, and the existing params.
     */
    public function withParam(string $name, string $value)
    {
        return new self([
            ...$this->params,
            $name => $value,
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function toArray()
    {
        return $this->params;
    }
}
