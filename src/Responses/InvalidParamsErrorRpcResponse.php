<?php
declare(strict_types=1);

namespace Beauty\JsonRPC\Responses;

class InvalidParamsErrorRpcResponse extends ErrorRpcResponse
{
    public const CODE = -32602;

    public const MESSAGE = 'Invalid params';
}