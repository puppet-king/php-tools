<?php

declare(strict_types=1);

namespace App\Command;

use App\Office\OfficeReader;
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
 * Office 文档读取命令
 *
 * 支持读取 DOCX、XLSX、PPTX 格式的 Office 文档内容
 */
#[AsCommand(
    name: 'office:read',
    description: '读取 Office 文档内容（DOCX、XLSX、PPTX）'
)]
final class OfficeCommand extends Command
{
    public function __construct(
        private readonly OfficeReader $officeReader,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDefinition(
            new InputDefinition([
                new InputArgument(
                    'path',
                    InputArgument::REQUIRED,
                    'Office 文档的绝对路径'
                ),
                new InputOption(
                    'format',
                    'f',
                    InputOption::VALUE_REQUIRED,
                    '输出格式：text（文本）或 json',
                    'text'
                ),
                new InputOption(
                    'sheet',
                    's',
                    InputOption::VALUE_REQUIRED,
                    '指定要输出的工作表名称（仅 XLSX 文件）'
                ),
            ])
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('path');
        $format = (string) $input->getOption('format');
        $sheetName = $input->getOption('sheet');

        // 验证路径是绝对路径
        if (!$this->isAbsolutePath($path)) {
            $output->writeln('<error>路径必须是绝对路径</error>');
            return ExitCode::USAGE;
        }

        try {
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            match ($extension) {
                'docx' => $this->handleDocx($path, $format, $output),
                'xlsx' => $this->handleXlsx($path, $format, $sheetName, $output),
                'pptx' => $this->handlePptx($path, $format, $output),
                default => throw new RuntimeException("不支持的文件格式：{$extension}。支持：docx, xlsx, pptx"),
            };

            return ExitCode::OK;
        } catch (RuntimeException $e) {
            $output->writeln("<error>错误：{$e->getMessage()}</error>");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * 处理 DOCX 文件读取
     *
     * @param string $path 文件路径
     * @param string $format 输出格式
     * @param OutputInterface $output 输出接口
     */
    private function handleDocx(string $path, string $format, OutputInterface $output): void
    {
        $content = $this->officeReader->readDocx($path);
        print_r("handleDocx $path $content");

        if ($format === 'json') {
            $output->writeln(json_encode(['content' => $content], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $output->writeln($content);
        }
    }

    /**
     * 处理 XLSX 文件读取
     *
     * @param string $path 文件路径
     * @param string $format 输出格式
     * @param string|null $sheetName 指定的工作表名称
     * @param OutputInterface $output 输出接口
     */
    private function handleXlsx(string $path, string $format, ?string $sheetName, OutputInterface $output): void
    {
        /** @var array<string, array> $sheets */
        $sheets = $this->officeReader->readXlsx($path);

        if ($sheetName !== null) {
            if (!isset($sheets[$sheetName])) {
                throw new RuntimeException("找不到工作表：$sheetName");
            }
            $this->outputSheet($sheetName, $sheets[$sheetName], $format, $output);
        } else {
            foreach ($sheets as $name => $data) {
                $this->outputSheet($name, $data, $format, $output);
                $output->writeln('');
            }
        }
    }

    /**
     * 输出单个工作表数据
     *
     * @param string $name 工作表名称
     * @param array $data 工作表数据
     * @param string $format 输出格式
     * @param OutputInterface $output 输出接口
     */
    private function outputSheet(string $name, array $data, string $format, OutputInterface $output): void
    {
        if ($format === 'json') {
            $output->writeln(json_encode(['sheet' => $name, 'data' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $output->writeln("<info>工作表：$name</info>");
            foreach ($data as $row) {
                $output->writeln(implode("\t", array_map(fn($cell) => (string) $cell, $row)));
            }
        }
    }

    /**
     * 处理 PPTX 文件读取
     *
     * @param string $path 文件路径
     * @param string $format 输出格式
     * @param OutputInterface $output 输出接口
     */
    private function handlePptx(string $path, string $format, OutputInterface $output): void
    {
        $slides = $this->officeReader->readPptx($path);

        if ($format === 'json') {
            $output->writeln(json_encode(['slides' => $slides], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            foreach ($slides as $index => $content) {
                $output->writeln("<info>幻灯片 " . ($index + 1) . '</info>');
                $output->writeln($content);
                $output->writeln('');
            }
        }
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
