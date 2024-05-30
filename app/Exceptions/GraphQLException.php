<?php

namespace App\Exceptions;

use Exception;
use GraphQL\Error\ClientAware;
use GraphQL\Error\ProvidesExtensions;

class GraphQLException extends Exception implements ClientAware, ProvidesExtensions
{

    protected string $reason;

    public function __construct(string $reason)
    {
        parent::__construct($reason);
        $this->reason = $reason;
    }


    public function isClientSafe(): bool
    {
        return true;
    }

    public function getExtensions(): ?array
    {
        return [
            'sonme'  => 'Internal Server Error',
            'reason' => $this->reason,
        ];
    }
}
