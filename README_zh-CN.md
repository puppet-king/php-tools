# YII3 PHP Tools

一个用于构建 AI Agent 的 PHP 工具包。本项目提供了一系列控制台命令和实用工具，供 AI Agent（如 Claude Code）用来与 PHP 应用程序交互并执行各种任务。

基于 Yii Framework 3 控制台应用程序模板。

## 要求

- PHP 8.2+
- Composer

## 安装

```bash
composer install
```

## 配置

复制环境变量文件并根据需要修改：

```bash
cp .env.example .env
```

## 运行命令

```bash
# 查看所有可用命令
./yii

# 运行示例命令
./yii echo
./yii echo "Hello World"
```

## 测试

```bash
# 运行所有测试
composer test

# 运行单元测试
vendor/bin/codecept run Unit

# 运行 CLI 测试
vendor/bin/codecept run Cli
```

## 静态分析

```bash
# 运行 Psalm
vendor/bin/psalm
```

## 项目结构

```
.
├── config/                      # 配置文件
│   ├── di/                      # 依赖注入配置
│   ├── environments/            # 环境特定配置
│   │   ├── dev/                 # 开发环境
│   │   ├── prod/                # 生产环境
│   │   └── test/                # 测试环境
│   ├── commands.php             # 命令配置
│   └── params.php               # 应用参数
├── src/                         # 源代码
│   └── Command/                 # 控制台命令
├── tests/                       # 测试文件
│   ├── Unit/                    # 单元测试
│   ├── Cli/                     # CLI 测试
│   └── Support/                 # 测试辅助
├── runtime/                     # 运行时文件
├── vendor/                      # Composer 依赖
├── composer.json                # Composer 配置
├── configuration.php            # 配置入口
└── yii                          # 控制台入口脚本
```

## 创建新命令

在 `src/Command/` 目录下创建新命令类：

```php
<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Yii\Console\ExitCode;

#[AsCommand(name: 'my-command', description: '命令描述')]
final class MyCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 命令逻辑
        return ExitCode::OK;
    }
}
```

然后在 `config/commands.php` 中注册命令。

## 环境配置

- **开发环境**: `.env` 设置 `YII_ENV=dev` 和 `YII_DEBUG=true`
- **测试环境**: `.env.test` 设置 `YII_ENV=test`
- **生产环境**: 设置 `YII_ENV=prod` 和 `YII_DEBUG=false`

## 许可证

BSD 3-Clause License

## 相关链接

- [Yii Framework 文档](https://www.yiiframework.com/docs)
- [Yii Console 组件](https://github.com/yiisoft/yii-console)
- [Symfony Console 组件](https://symfony.com/doc/current/console.html)
