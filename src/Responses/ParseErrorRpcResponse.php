<?php
declare(strict_types=1);

namespace Beauty\JsonRPC\Responses;

class ParseErrorRpcResponse extends ErrorRpcResponse
{
    public const CODE = -32700;

    public const MESSAGE = 'Parse error';
}