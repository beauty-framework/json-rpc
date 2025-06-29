<?php
declare(strict_types=1);

namespace Beauty\JsonRPC\Responses;

class ServerErrorRpcResponse extends ErrorRpcResponse
{
    public const CODE = -32000;

    public const MESSAGE = 'Server error';
}