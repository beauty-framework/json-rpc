<?php
declare(strict_types=1);

namespace Beauty\JsonRPC\Console;

use Beauty\Cli\Console\Contracts\CommandsRegistryInterface;
use Beauty\JsonRPC\Console\Commands\Generate\HandlerCommand;
use Beauty\JsonRPC\Console\Commands\InstallCommand;

class RegisterCommands implements CommandsRegistryInterface
{

    /**
     * @return \class-string[]
     */
    public static function commands(): array
    {
        return [
            InstallCommand::class,
            HandlerCommand::class,
        ];
    }
}