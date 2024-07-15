<?php

namespace OpenAI\Resources\Concerns;

use OpenAI\Contracts\TransporterContract;

trait Transportable
{
    public $transporter;
    /**
     * Creates a Client instance with the given API token.
     */
    public function __construct(TransporterContract $transporter)
    {
        $this->transporter = $transporter;
    }
}
