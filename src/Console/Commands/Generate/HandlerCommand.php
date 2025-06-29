<?php
declare(strict_types=1);

namespace Beauty\JsonRPC\Console\Commands\Generate;

use Beauty\Cli\Commands\Generate\AbstractGeneratorCommand;

class HandlerCommand extends AbstractGeneratorCommand
{

    /**
     * @return string
     */
    public function name(): string
    {
        return 'generate:handler';
    }

    /**
     * @return string|null
     */
    public function description(): string|null
    {
        return 'Generate JsonRPC Handler';
    }

    /**
     * @return string
     */
    protected function stubPath(): string
    {
        return __DIR__ . '/../../../../stubs/handler.stub';
    }

    /**
     * @return string
     */
    protected function baseNamespace(): string
    {
        return 'App\RpcHandlers';
    }

    /**
     * @return string
     */
    protected function baseDirectory(): string
    {
        return 'app/RpcHandlers';
    }

    /**
     * @return string
     */
    protected function suffix(): string
    {
        return 'Handler';
    }
}