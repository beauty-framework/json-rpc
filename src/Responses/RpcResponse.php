<?php
declare(strict_types=1);

namespace Beauty\JsonRPC\Responses;

use Beauty\Http\Response\JsonResponse;

class RpcResponse extends JsonResponse implements RpcResponseInterface
{
    /**
     * @param array|\JsonSerializable|string $data
     * @param string|int|null $id
     * @param array $headers
     * @param int $flags
     * @param string $version
     * @param string|null $reason
     */
    public function __construct(
        array|\JsonSerializable|string $data = [],
        string|int|null $id = null,
        array $headers = [],
        int $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        string $version = '2.0',
        ?string $reason = null,
    )
    {
        $body = $this->generateResponse($data, $version, $id);

        parent::__construct(200, $body, $headers, $flags, $version, $reason);
    }

    /**
     * @param array|\JsonSerializable|string $data
     * @param string $version
     * @param string|int|null $id
     * @return array
     */
    public function generateResponse(array|\JsonSerializable|string $data, string $version, string|int|null $id = null): array
    {
        return [
            'jsonrpc' => $version,
            'result' => $data,
            'id' => $id,
        ];
    }
}