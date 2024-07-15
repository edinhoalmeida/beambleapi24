<?php

namespace OpenAI\Testing\Requests;

final class TestRequest
{

    private $resource;
	private $method;  
	private $parameters;

    /**
     * @param  array<string, mixed>|string|null  $parameters
     */
    public function __construct(string $resource, string $method,  array|string|null $parameters)
    {
		$this->resource = $resource;
		$this->method = $method;  
		$this->parameters = $parameters;
    }

    public function resource()
    {
        return $this->resource;
    }

    public function method()
    {
        return $this->method;
    }

    /**
     * @return array<string, mixed>|string|null
     */
    public function parameters()
    {
        return $this->parameters;
    }
}
