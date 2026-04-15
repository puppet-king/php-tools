<?php

declare(strict_types=1);

use App\Command\EchoCommand;
use App\Command\FileCommand;
use App\Command\OfficeCommand;

return [
    'echo' => EchoCommand::class,
    'office:read' => OfficeCommand::class,
    'file' => FileCommand::class,
];
