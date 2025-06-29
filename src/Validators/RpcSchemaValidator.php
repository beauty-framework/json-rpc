<?php
declare(strict_types=1);

namespace Beauty\JsonRPC\Validators;

class RpcSchemaValidator
{
    /**
     * @param array $data
     * @return bool
     */
    public static function isValidRequest(array $data): bool
    {
        return isset($data['jsonrpc']) && $data['jsonrpc'] === '2.0'
            && isset($data['method']) && is_string($data['method']);
    }
}