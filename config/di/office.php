<?php

declare(strict_types=1);

use App\File\FileHandler;
use App\Office\OfficeReader;

return [
    OfficeReader::class => [
        'class' => OfficeReader::class,
    ],
    FileHandler::class => [
        'class' => FileHandler::class,
    ],
];
