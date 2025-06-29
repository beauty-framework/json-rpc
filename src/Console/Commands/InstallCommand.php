<?php
declare(strict_types=1);

namespace Beauty\JsonRPC\Console\Commands;

use Beauty\Cli\CLI;
use Beauty\Cli\Console\AbstractCommand;

class InstallCommand extends AbstractCommand
{

    public function name(): string
    {
        return 'jsonrpc:install';
    }

    public function description(): string|null
    {
        return 'Install config for JsonRPC';
    }

    public function handle(array $args): int
    {
        $base = base_path('/' . DIRECTORY_SEPARATOR);
        $this->info('Installing JsonRPC...');

        $this->info('Copying files...');

        $stub = __DIR__ . '/../../../stubs/json-rpc.php';
        $target = $base . '/config/json-rpc.php';

        copy($stub, $target);

        $this->success('Successfully installed JsonRPC config');
        $this->line('In workers/http-worker.php add line:');
        $this->warn('\Beauty\JsonRPC\JsonRpcServer::setHandlers(require base_path(\'config/json-rpc.php\'));');

        return CLI::SUCCESS;
    }
}