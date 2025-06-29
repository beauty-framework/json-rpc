<?php
declare(strict_types=1);

namespace Beauty\JsonRPC\Responses;

class MethodNotFoundErrorRpcResponse extends ErrorRpcResponse
{
    public const CODE = -32601;
    public const MESSAGE = 'Method not found';
}