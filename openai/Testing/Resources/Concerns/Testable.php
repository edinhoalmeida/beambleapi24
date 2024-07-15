<?php

namespace OpenAI\Testing\Resources\Concerns;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\StreamResponse;
use OpenAI\Testing\ClientFake;
use OpenAI\Testing\Requests\TestRequest;

trait Testable
{

    public $fake;

    public function __construct(ClientFake $fake)
    {
        $this->fake = $fake;
    }

    abstract protected function resource();

    /**
     * @param  array<string, mixed>|string|null  $parameters
     */
    protected function record(string $method, array|string|null $parameters = null)
    {
        return $this->fake->record(new TestRequest($this->resource(), $method, $parameters));
    }

    /**
     * @param  callable|int|null  $callback
     */
    public function assertSent($callback = null)
    {
        $this->fake->assertSent($this->resource(), $callback);
    }

    /**
     * @param  callable|int|null  $callback
     */
    public function assertNotSent(callable $callback = null)
    {
        $this->fake->assertNotSent($this->resource(), $callback);
    }
}
