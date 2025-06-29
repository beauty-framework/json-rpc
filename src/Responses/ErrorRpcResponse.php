<?php
declare(strict_types=1);

namespace Beauty\JsonRPC\Responses;

abstract class ErrorRpcResponse extends RpcResponse
{
    const CODE = 0;

    const MESSAGE = '';

    /**
     * @param array|\JsonSerializable|string $data
     * @param string $version
     * @param int|string|null $id
     * @return array
     */
    public function generateResponse(array|\JsonSerializable|string $data, string $version, int|string|null $id = null): array
    {
        return [
            'jsonrpc' => $version,
            'error' => [
                'code' => static::CODE,
                'message' => static::MESSAGE,
                'data' => $data,
            ],
            'id' => $id,
        ];
    }
}