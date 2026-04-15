# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

YII3 console application template. PHP 8.2+ project using Symfony Console for commands and Codeception for testing.

## Commands

```bash
# Run tests
composer test

# Run static analysis (Psalm, errorLevel=1 - strictest)
vendor/bin/psalm

# Run console commands
./yii <command-name>
```

## Coding Standards

- **Commands**: Use Symfony Console `#[AsCommand]` attributes. Follow the `EchoCommand` pattern for new commands.
- **Types**: Strict types declared (`declare(strict_types=1);`)
- **Namespace**: `App\` for src, `App\Tests\` for tests
- **DI**: Configure services in `config/di/*.php`
- **Params**: Environment-specific config in `config/environments/{dev,prod,test}/params.php`

### PHP Code Rules

See `.claude/rules/php-coding-standards.md` for coding standards:

1. **字符串插值**: 简单变量不使用大括号
   - ✅ `"File not found: $filePath"`
   - ❌ `"File not found: {$filePath}"`

2. **类型注释**: 可推断类型时不使用 `@var`
   - ✅ `$content = [];`
   - ❌ `/** @var array */ $content = [];`
   - 例外：Symfony Console 输入、外部库返回类型不明确时需要 `@var`

3. **导入使用**: 已导入的类不使用完全限定名
   - ✅ `IOFactory::load($filePath)`
   - ❌ `\PhpOffice\PhpSpreadsheet\IOFactory::load($filePath)`

4. **静态分析**: 使用 Psalm (errorLevel=1)，允许抑制外部库混合类型警告

## Testing

Codeception with unit and CLI suites. New features require tests in appropriate suite:
- Unit tests: `tests/Unit/` - use `UnitTester`
- CLI tests: `tests/Cli/` - use `CliTester` for shell command verification

## Environment Setup

- `.env` for dev, `.env.test` for test environment
- Runtime output in `runtime/`
- Vendor not committed

## Configuration

Uses `yiisoft/config` plugin. Config loaded via `configuration.php`:
- `params.php` - application parameters
- `di/*.php` - dependency injection definitions
- Environment overrides in `config/environments/{dev,prod,test}/`
