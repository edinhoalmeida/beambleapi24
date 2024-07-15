<?php

namespace OpenAI\Resources;

use OpenAI\Contracts\Resources\FilesContract;
use OpenAI\Responses\Files\CreateResponse;
use OpenAI\Responses\Files\DeleteResponse;
use OpenAI\Responses\Files\ListResponse;
use OpenAI\Responses\Files\RetrieveResponse;
use OpenAI\ValueObjects\Transporter\Payload;

final class Files implements FilesContract
{
    use Concerns\Transportable;

    /**
     * Returns a list of files that belong to the user's organization.
     *
     * @see https://beta.openai.com/docs/api-reference/files/list
     */
    public function list()
    {
        $payload = Payload::list('files');

        /** @var array{object, data<int, array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null}>} $result */
        $result = $this->transporter->requestObject($payload);

        return ListResponse::from($result);
    }

    /**
     * Returns information about a specific file.
     *
     * @see https://beta.openai.com/docs/api-reference/files/retrieve
     */
    public function retrieve(string $file)
    {
        $payload = Payload::retrieve('files', $file);

        /** @var array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null} $result */
        $result = $this->transporter->requestObject($payload);

        return RetrieveResponse::from($result);
    }

    /**
     * Returns the contents of the specified file.
     *
     * @see https://beta.openai.com/docs/api-reference/files/retrieve-content
     */
    public function download(string $file)
    {
        $payload = Payload::retrieveContent('files', $file);

        return $this->transporter->requestContent($payload);
    }

    /**
     * Upload a file that contains document(s) to be used across various endpoints/features.
     *
     * @see https://beta.openai.com/docs/api-reference/files/upload
     *
     * @param  array<string, mixed>  $parameters
     */
    public function upload(array $parameters)
    {
        $payload = Payload::upload('files', $parameters);

        /** @var array{id, object, created_at: int, bytes: int, filename, purpose, status, status_details<array-key, mixed>|string|null} $result */
        $result = $this->transporter->requestObject($payload);

        return CreateResponse::from($result);
    }

    /**
     * Delete a file.
     *
     * @see https://beta.openai.com/docs/api-reference/files/delete
     */
    public function delete(string $file)
    {
        $payload = Payload::delete('files', $file);

        /** @var array{id, object, deleted} $result */
        $result = $this->transporter->requestObject($payload);

        return DeleteResponse::from($result);
    }
}
