<?php

declare(strict_types=1);

namespace App\Command;

use App\File\FileHandler;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Yii\Console\ExitCode;

/**
 * 文件操作命令
 *
 * 提供文件读取、写入、复制、移动、删除、信息查询等功能
 */
#[AsCommand(
    name: 'file',
    description: '文件操作：读取、写入、复制、移动、删除、信息查询'
)]
final class FileCommand extends Command
{
    public function __construct(
        private FileHandler $fileHandler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDefinition(
            new InputDefinition([
                new InputArgument(
                    'action',
                    InputArgument::REQUIRED,
                    '操作类型：read（读取）, write（写入）, copy（复制）, move（移动）, delete（删除）, info（信息）, exists（存在性）, list（列表）'
                ),
                new InputArgument(
                    'path',
                    InputArgument::REQUIRED,
                    '文件路径（必须是绝对路径）'
                ),
                new InputArgument(
                    'content',
                    InputArgument::OPTIONAL,
                    '要写入或追加的内容（用于 write/append 操作）'
                ),
                new InputOption(
                    'target',
                    't',
                    InputOption::VALUE_REQUIRED,
                    '目标路径（用于 copy/move 操作）'
                ),
                new InputOption(
                    'format',
                    'f',
                    InputOption::VALUE_REQUIRED,
                    '输出格式：text（文本）或 json',
                    'text'
                ),
                new InputOption(
                    'pattern',
                    'p',
                    InputOption::VALUE_REQUIRED,
                    'Glob 匹配模式（用于 list 操作），如 "*.txt"'
                ),
            ])
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $action = (string) $input->getArgument('action');
        $path = (string) $input->getArgument('path');
        /** @var string|null */
        $content = $input->getArgument('content');
        /** @var string|null */
        $target = $input->getOption('target');
        $format = (string) $input->getOption('format');
        /** @var string|null */
        $pattern = $input->getOption('pattern');

        // 验证路径是绝对路径（list 操作除外）
        if (!in_array($action, ['list']) && !$this->isAbsolutePath($path)) {
            $output->writeln('<error>路径必须是绝对路径</error>');
            return ExitCode::USAGE;
        }

        try {
            return match ($action) {
                'read' => $this->handleRead($path, $format, $output),
                'write' => $this->handleWrite($path, $content ?? '', $output),
                'append' => $this->handleAppend($path, $content ?? '', $output),
                'copy' => $this->handleCopy($path, $target ?? '', $output),
                'move' => $this->handleMove($path, $target ?? '', $output),
                'delete' => $this->handleDelete($path, $output),
                'info' => $this->handleInfo($path, $format, $output),
                'exists' => $this->handleExists($path, $format, $output),
                'list' => $this->handleList($path, $pattern, $format, $output),
                default => throw new RuntimeException("未知操作：$action"),
            };
        } catch (RuntimeException $e) {
            $output->writeln("<error>错误：{$e->getMessage()}</error>");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * 处理读取文件操作
     *
     * @param string $path 文件路径
     * @param string $format 输出格式
     * @param OutputInterface $output 输出接口
     * @return int 退出码
     */
    private function handleRead(string $path, string $format, OutputInterface $output): int
    {
        $content = $this->fileHandler->read($path);

        if ($format === 'json') {
            $output->writeln(json_encode(['content' => $content], JSON_UNESCAPED_UNICODE));
        } else {
            $output->writeln($content);
        }

        return ExitCode::OK;
    }

    /**
     * 处理写入文件操作
     *
     * @param string $path 文件路径
     * @param string $content 要写入的内容
     * @param OutputInterface $output 输出接口
     * @return int 退出码
     */
    private function handleWrite(string $path, string $content, OutputInterface $output): int
    {
        $this->fileHandler->write($path, $content);
        $output->writeln("<info>文件已写入：$path</info>");
        return ExitCode::OK;
    }

    /**
     * 处理追加文件操作
     *
     * @param string $path 文件路径
     * @param string $content 要追加的内容
     * @param OutputInterface $output 输出接口
     * @return int 退出码
     */
    private function handleAppend(string $path, string $content, OutputInterface $output): int
    {
        $this->fileHandler->append($path, $content);
        $output->writeln("<info>内容已追加到：$path</info>");
        return ExitCode::OK;
    }

    /**
     * 处理复制文件操作
     *
     * @param string $source 源文件路径
     * @param string $target 目标文件路径
     * @param OutputInterface $output 输出接口
     * @return int 退出码
     */
    private function handleCopy(string $source, string $target, OutputInterface $output): int
    {
        if ($target === '') {
            throw new RuntimeException('复制操作需要指定目标路径');
        }
        $result = $this->fileHandler->copy($source, $target);
        $output->writeln("<info>已复制到：$result</info>");
        return ExitCode::OK;
    }

    /**
     * 处理移动文件操作
     *
     * @param string $source 源文件路径
     * @param string $target 目标文件路径
     * @param OutputInterface $output 输出接口
     * @return int 退出码
     */
    private function handleMove(string $source, string $target, OutputInterface $output): int
    {
        if ($target === '') {
            throw new RuntimeException('移动操作需要指定目标路径');
        }
        $result = $this->fileHandler->move($source, $target);
        $output->writeln("<info>已移动到：$result</info>");
        return ExitCode::OK;
    }

    /**
     * 处理删除文件操作
     *
     * @param string $path 文件路径
     * @param OutputInterface $output 输出接口
     * @return int 退出码
     */
    private function handleDelete(string $path, OutputInterface $output): int
    {
        $this->fileHandler->delete($path);
        $output->writeln("<info>已删除：$path</info>");
        return ExitCode::OK;
    }

    /**
     * 处理文件信息查询操作
     *
     * @param string $path 文件路径
     * @param string $format 输出格式
     * @param OutputInterface $output 输出接口
     * @return int 退出码
     */
    private function handleInfo(string $path, string $format, OutputInterface $output): int
    {
        $info = $this->fileHandler->getFileInfo($path);

        if ($format === 'json') {
            $output->writeln(json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $output->writeln("路径：{$info['path']}");
            $output->writeln("文件名：{$info['filename']}");
            $output->writeln("大小：{$info['size']} 字节");
            $output->writeln("修改时间：{$info['modified']}");
            $output->writeln("可读：" . ($info['is_readable'] ? '是' : '否'));
            $output->writeln("可写：" . ($info['is_writable'] ? '是' : '否'));
        }

        return ExitCode::OK;
    }

    /**
     * 处理文件存在性检查操作
     *
     * @param string $path 文件路径
     * @param string $format 输出格式
     * @param OutputInterface $output 输出接口
     * @return int 退出码
     */
    private function handleExists(string $path, string $format, OutputInterface $output): int
    {
        $exists = $this->fileHandler->exists($path);

        if ($format === 'json') {
            $output->writeln(json_encode(['exists' => $exists, 'path' => $path]));
        } else {
            $output->writeln($exists ? "<info>文件存在：$path</info>" : "<error>文件不存在：$path</error>");
        }

        return $exists ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
    }

    /**
     * 处理目录文件列表操作
     *
     * @param string $directory 目录路径
     * @param string|null $pattern Glob 匹配模式
     * @param string $format 输出格式
     * @param OutputInterface $output 输出接口
     * @return int 退出码
     */
    private function handleList(string $directory, ?string $pattern, string $format, OutputInterface $output): int
    {
        $files = $this->fileHandler->listFiles($directory, $pattern);

        if ($format === 'json') {
            $output->writeln(json_encode(['directory' => $directory, 'files' => $files], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $output->writeln("<info>目录中的文件：$directory</info>");
            foreach ($files as $file) {
                $output->writeln("  $file");
            }
        }

        return ExitCode::OK;
    }

    /**
     * 检查路径是否为绝对路径
     *
     * @param string $path 要检查的路径
     * @return bool 是绝对路径返回 true
     */
    private function isAbsolutePath(string $path): bool
    {
        // Windows 路径格式 (如 C:\)
        if (preg_match('/^[A-Z]:[\\\\\/]/i', $path)) {
            return true;
        }
        // Unix/Linux 路径格式 (如 /home/)
        if (str_starts_with($path, '/')) {
            return true;
        }
        return false;
    }
}
