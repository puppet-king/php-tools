<?php

declare(strict_types=1);

namespace App\File;

use RuntimeException;
use SplFileInfo;

/**
 * 文件处理器
 *
 * 提供常用的文件操作功能：读取、写入、复制、移动、删除等
 */
final class FileHandler
{
    /**
     * 读取文件内容
     *
     * @param string $filePath 文件的绝对路径
     * @return string 文件内容
     * @throws RuntimeException 当文件不存在或读取失败时抛出
     */
    public function read(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("File not found: $filePath");
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new RuntimeException("Failed to read file: $filePath");
        }

        return $content;
    }

    /**
     * 写入内容到文件
     *
     * @param string $filePath 文件的绝对路径
     * @param string $content 要写入的内容
     * @param bool $createDir 是否自动创建目录（如果目录不存在）
     * @return bool 成功返回 true
     * @throws RuntimeException 当写入失败时抛出
     */
    public function write(string $filePath, string $content, bool $createDir = true): bool
    {
        $dir = dirname($filePath);
        if ($createDir && !is_dir($dir)) {
            $result = mkdir($dir, 0755, true);
            if (!$result) {
                throw new RuntimeException("Failed to create directory: $dir");
            }
        }

        $result = file_put_contents($filePath, $content);
        if ($result === false) {
            throw new RuntimeException("Failed to write file: $filePath");
        }

        return true;
    }

    /**
     * 追加内容到文件
     *
     * @param string $filePath 文件的绝对路径
     * @param string $content 要追加的内容
     * @return bool 成功返回 true
     * @throws RuntimeException 当文件不存在或追加失败时抛出
     */
    public function append(string $filePath, string $content): bool
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("File not found: $filePath");
        }

        $result = file_put_contents($filePath, $content, FILE_APPEND);
        if ($result === false) {
            throw new RuntimeException("Failed to append file: $filePath");
        }

        return true;
    }

    /**
     * 删除文件
     *
     * @param string $filePath 文件的绝对路径
     * @return bool 成功返回 true
     * @throws RuntimeException 当文件不存在或删除失败时抛出
     */
    public function delete(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("File not found: $filePath");
        }

        $result = unlink($filePath);
        if (!$result) {
            throw new RuntimeException("Failed to delete file: $filePath");
        }

        return true;
    }

    /**
     * 复制文件
     *
     * @param string $source 源文件路径
     * @param string $destination 目标文件路径
     * @return string 目标文件路径
     * @throws RuntimeException 当源文件不存在或复制失败时抛出
     */
    public function copy(string $source, string $destination): string
    {
        if (!file_exists($source)) {
            throw new RuntimeException("Source file not found: $source");
        }

        $dir = dirname($destination);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $result = copy($source, $destination);
        if (!$result) {
            throw new RuntimeException("Failed to copy file from $source to $destination");
        }

        return $destination;
    }

    /**
     * 移动文件
     *
     * @param string $source 源文件路径
     * @param string $destination 目标文件路径
     * @return string 目标文件路径
     * @throws RuntimeException 当源文件不存在或移动失败时抛出
     */
    public function move(string $source, string $destination): string
    {
        if (!file_exists($source)) {
            throw new RuntimeException("Source file not found: $source");
        }

        $dir = dirname($destination);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $result = rename($source, $destination);
        if (!$result) {
            throw new RuntimeException("Failed to move file from $source to $destination");
        }

        return $destination;
    }

    /**
     * 获取文件信息
     *
     * @param string $filePath 文件的绝对路径
     * @return array{path: string, filename: string, size: int, modified: string, is_readable: bool, is_writable: bool}
     *         文件信息：路径、文件名、大小、修改时间、是否可读、是否可写
     * @throws RuntimeException 当文件不存在时抛出
     */
    public function getFileInfo(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("File not found: $filePath");
        }

        $fileInfo = new SplFileInfo($filePath);

        return [
            'path' => $fileInfo->getRealPath(),
            'filename' => $fileInfo->getFilename(),
            'size' => $fileInfo->getSize(),
            'modified' => date('Y-m-d H:i:s', $fileInfo->getMTime()),
            'is_readable' => $fileInfo->isReadable(),
            'is_writable' => $fileInfo->isWritable(),
        ];
    }

    /**
     * 检查文件是否存在
     *
     * @param string $filePath 文件路径
     * @return bool 文件存在返回 true
     */
    public function exists(string $filePath): bool
    {
        return file_exists($filePath);
    }

    /**
     * 获取文件扩展名
     *
     * @param string $filePath 文件路径
     * @return string 文件扩展名（不含点）
     */
    public function getExtension(string $filePath): string
    {
        return pathinfo($filePath, PATHINFO_EXTENSION);
    }

    /**
     * 列出目录下的文件
     *
     * @param string $directory 目录路径
     * @param string|null $pattern 可选的 glob 匹配模式，如 "*.txt"
     * @return array<string> 文件路径列表
     * @throws RuntimeException 当目录不存在时抛出
     */
    public function listFiles(string $directory, ?string $pattern = null): array
    {
        if (!is_dir($directory)) {
            throw new RuntimeException("Directory not found: $directory");
        }

        if ($pattern !== null) {
            $files = glob(rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $pattern);
            return $files === false ? [] : $files;
        }

        $files = [];
        $iterator = new \DirectoryIterator($directory);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile()) {
                $files[] = $fileInfo->getPathname();
            }
        }

        return $files;
    }
}
