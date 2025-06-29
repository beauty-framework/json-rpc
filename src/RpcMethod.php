<?php
declare(strict_types=1);

namespace Beauty\JsonRPC;

#[\Attribute(\Attribute::TARGET_METHOD)]
class RpcMethod
{
    /**
     * @param string $name
     */
    public function __construct(
        public string $name,
    )
    {
    }
}