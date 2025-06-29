<?php
declare(strict_types=1);

namespace Beauty\JsonRPC\Responses;

class InvalidRequestErrorRpcResponse extends ErrorRpcResponse
{
    public const CODE = -32600;

    public const MESSAGE = 'Invalid Request';
}