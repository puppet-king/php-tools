<?php

declare(strict_types=1);

namespace App\Office;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use RuntimeException;

/**
 * Office 文档读取器
 *
 * 支持 DOCX、XLSX、PPTX 格式的 Office 文档读取
 */
final class OfficeReader
{
    /**
     * 读取 DOCX 文件内容
     *
     * @param string $filePath DOCX 文件的绝对路径
     * @return string 文档文本内容
     * @throws RuntimeException 当文件不存在时抛出
     */
    public function readDocx(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("File not found: $filePath");
        }

        $phpWord = WordIOFactory::load($filePath);
        $content = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $content[] = $element->getText();
                }
            }
        }

        return implode("\n", $content);
    }

    /**
     * 读取 XLSX 文件内容
     *
     * @param string $filePath XLSX 文件的绝对路径
     * @return array<string, array> 关联数组，键为工作表名，值为二维数组数据
     * @throws RuntimeException 当文件不存在时抛出
     */
    public function readXlsx(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("File not found: $filePath");
        }

        $spreadsheet = IOFactory::load($filePath);
        $result = [];

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $sheetName = $sheet->getTitle();
            $result[$sheetName] = $sheet->toArray();
        }

        return $result;
    }

    /**
     * 读取 PPTX 文件内容
     *
     * @param string $filePath PPTX 文件的绝对路径
     * @return array<int, string> 幻灯片文本内容数组
     * @throws RuntimeException 当文件不存在时抛出
     */
    public function readPptx(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("File not found: $filePath");
        }

        $phpWord = WordIOFactory::load($filePath);
        $slides = [];

        foreach ($phpWord->getSections() as $section) {
            $slideContent = [];
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $slideContent[] = $element->getText();
                }
            }
            $slides[] = implode("\n", $slideContent);
        }

        return $slides;
    }

    /**
     * 获取文件信息
     *
     * @param string $filePath 文件的绝对路径
     * @return array{path: string, size: int, modified: string} 文件信息数组
     * @throws RuntimeException 当文件不存在时抛出
     */
    public function getFileInfo(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("File not found: $filePath");
        }

        return [
            'path' => $filePath,
            'size' => filesize($filePath),
            'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
        ];
    }
}
