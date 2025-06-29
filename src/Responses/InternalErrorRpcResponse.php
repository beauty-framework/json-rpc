<?php
declare(strict_types=1);

namespace Beauty\JsonRPC\Responses;

class InternalErrorRpcResponse extends ErrorRpcResponse
{
    public const CODE = -32603;

    public const MESSAGE = 'Internal error';
}