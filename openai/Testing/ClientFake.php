<?php

namespace OpenAI\Testing;

use OpenAI\Contracts\ClientContract;
use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\StreamResponse;
use OpenAI\Testing\Requests\TestRequest;
use OpenAI\Testing\Resources\AudioTestResource;
use OpenAI\Testing\Resources\ChatTestResource;
use OpenAI\Testing\Resources\CompletionsTestResource;
use OpenAI\Testing\Resources\EditsTestResource;
use OpenAI\Testing\Resources\EmbeddingsTestResource;
use OpenAI\Testing\Resources\FilesTestResource;
use OpenAI\Testing\Resources\FineTunesTestResource;
use OpenAI\Testing\Resources\ImagesTestResource;
use OpenAI\Testing\Resources\ModelsTestResource;
use OpenAI\Testing\Resources\ModerationsTestResource;
use PHPUnit\Framework\Assert as PHPUnit;
use Throwable;

/**
 * @noRector Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector
 */
class ClientFake implements ClientContract
{
    /**
     * @var array<array-key, TestRequest>
     */
    private array $requests = [];
    private array $responses = [];

    /**
     * @param  array<array-key, ResponseContract|StreamResponse|string>  $responses
     */
    public function __construct(array $responses = [])
    {
         $this->responses = $responses;
    }

    /**
     * @param  array<array-key, Response>  $responses
     */
    public function addResponses(array $responses)
    {
        $this->responses = [...$this->responses, ...$responses];
    }

    /**
     * @param  callable|int|null  $callback
     */
    public function assertSent(string $resource, $callback = null)
    {
        if (is_int($callback)) {
            $this->assertSentTimes($resource, $callback);

            return;
        }

        PHPUnit::assertTrue(
            $this->sent($resource, $callback) !== [],
            "The expected [{$resource}] request was not sent."
        );
    }

    protected function assertSentTimes(string $resource, int $times = 1)
    {
        $count = count($this->sent($resource));

        PHPUnit::assertSame(
            $times, $count,
            "The expected [{$resource}] resource was sent {$count} times instead of {$times} times."
        );
    }

    /**
     * @return mixed[]
     */
    protected function sent(string $resource, callable $callback = null)
    {
        if (! $this->hasSent($resource)) {
            return [];
        }

        $callback = $callback ?: fn () => true;

        return array_filter($this->resourcesOf($resource), fn (TestRequest $resource) => $callback($resource->method(), $resource->parameters()));
    }

    protected function hasSent(string $resource)
    {
        return $this->resourcesOf($resource) !== [];
    }

    public function assertNotSent(string $resource, callable $callback = null)
    {
        PHPUnit::assertCount(
            0, $this->sent($resource, $callback),
            "The unexpected [{$resource}] request was sent."
        );
    }

    public function assertNothingSent()
    {
        $resourceNames = implode(
            separator: ', ',
            array: array_map(fn (TestRequest $request) => $request->resource(), $this->requests)
        );

        PHPUnit::assertEmpty($this->requests, 'The following requests were sent unexpectedly: '.$resourceNames);
    }

    /**
     * @return array<array-key, TestRequest>
     */
    protected function resourcesOf(string $type)
    {
        return array_filter($this->requests, fn (TestRequest $request) => $request->resource() === $type);
    }

    public function record(TestRequest $request)
    {
        $this->requests[] = $request;

        $response = array_shift($this->responses);

        if (is_null($response)) {
            throw new \Exception('No fake responses left.');
        }

        if ($response instanceof Throwable) {
            throw $response;
        }

        return $response;
    }

    public function completions()
    {
        return new CompletionsTestResource($this);
    }

    public function chat()
    {
        return new ChatTestResource($this);
    }

    public function embeddings()
    {
        return new EmbeddingsTestResource($this);
    }

    public function audio()
    {
        return new AudioTestResource($this);
    }

    public function edits()
    {
        return new EditsTestResource($this);
    }

    public function files()
    {
        return new FilesTestResource($this);
    }

    public function models()
    {
        return new ModelsTestResource($this);
    }

    public function fineTunes()
    {
        return new FineTunesTestResource($this);
    }

    public function moderations()
    {
        return new ModerationsTestResource($this);
    }

    public function images()
    {
        return new ImagesTestResource($this);
    }
}
