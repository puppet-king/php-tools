# YII3 PHP Tools

A toolkit for building AI agents with PHP. This project provides a collection of console commands and utilities that can be used by AI agents (such as Claude Code) to interact with PHP applications and perform various tasks.

Built on Yii Framework 3 console application template.

## Requirements

- PHP 8.2+
- Composer

## Installation

```bash
composer install
```

## Configuration

Copy the environment file and modify as needed:

```bash
cp .env.example .env
```

## Running Commands

```bash
# List all available commands
./yii

# Run example command
./yii echo
./yii echo "Hello World"
```

## Testing

```bash
# Run all tests
composer test

# Run unit tests
vendor/bin/codecept run Unit

# Run CLI tests
vendor/bin/codecept run Cli
```

## Static Analysis

```bash
# Run Psalm
vendor/bin/psalm
```

## Project Structure

```
.
├── config/                      # Configuration files
│   ├── di/                      # Dependency injection config
│   ├── environments/            # Environment-specific config
│   │   ├── dev/                 # Development environment
│   │   ├── prod/                # Production environment
│   │   └── test/                # Test environment
│   ├── commands.php             # Command configuration
│   └── params.php               # Application parameters
├── src/                         # Source code
│   └── Command/                 # Console commands
├── tests/                       # Test files
│   ├── Unit/                    # Unit tests
│   ├── Cli/                     # CLI tests
│   └── Support/                 # Test helpers
├── runtime/                     # Runtime files
├── vendor/                      # Composer dependencies
├── composer.json                # Composer configuration
├── configuration.php            # Configuration entry
└── yii                          # Console entry script
```

## Creating a New Command

Create a new command class in `src/Command/`:

```php
<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Yii\Console\ExitCode;

#[AsCommand(name: 'my-command', description: 'Command description')]
final class MyCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Command logic
        return ExitCode::OK;
    }
}
```

Then register the command in `config/commands.php`.

## Environment Configuration

- **Development**: `.env` with `YII_ENV=dev` and `YII_DEBUG=true`
- **Test**: `.env.test` with `YII_ENV=test`
- **Production**: Set `YII_ENV=prod` and `YII_DEBUG=false`

## License

BSD 3-Clause License

## Related Links

- [Yii Framework Documentation](https://www.yiiframework.com/docs)
- [Yii Console Component](https://github.com/yiisoft/yii-console)
- [Symfony Console Component](https://symfony.com/doc/current/console.html)
