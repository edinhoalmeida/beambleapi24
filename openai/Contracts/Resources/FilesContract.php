<?php

namespace OpenAI\Contracts\Resources;

use OpenAI\Responses\Files\CreateResponse;
use OpenAI\Responses\Files\DeleteResponse;
use OpenAI\Responses\Files\ListResponse;
use OpenAI\Responses\Files\RetrieveResponse;

interface FilesContract
{
    /**
     * Returns a list of files that belong to the user's organization.
     *
     * @see https://beta.openai.com/docs/api-reference/files/list
     */
    public function list();

    /**
     * Returns information about a specific file.
     *
     * @see https://beta.openai.com/docs/api-reference/files/retrieve
     */
    public function retrieve(string $file);

    /**
     * Returns the contents of the specified file.
     *
     * @see https://beta.openai.com/docs/api-reference/files/retrieve-content
     */
    public function download(string $file);

    /**
     * Upload a file that contains document(s) to be used across various endpoints/features.
     *
     * @see https://beta.openai.com/docs/api-reference/files/upload
     *
     * @param  array<string, mixed>  $parameters
     */
    public function upload(array $parameters);

    /**
     * Delete a file.
     *
     * @see https://beta.openai.com/docs/api-reference/files/delete
     */
    public function delete(string $file);
}