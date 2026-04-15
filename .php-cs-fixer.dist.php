<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

/**
 * PHP 代码规范配置
 *
 * 安装：composer require --dev friendsofphp/php-cs-fixer
 * 运行：vendor/bin/php-cs-fixer fix
 */

$finder = Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/config',
        __DIR__ . '/tests',
    ])
    ->name('*.php');

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        // ========== PSR 规范 ==========
        '@PSR12' => true,
        '@PSR12:risky' => true,

        // ========== 代码风格 ==========
        // 数组语法
        'array_syntax' => ['syntax' => 'short'], // 使用短数组语法 []
        'array_indentation' => true, // 数组缩进
        'whitespace_after_comma_in_array' => true, // 逗号后加空格
        'trailing_comma_in_multiline' => ['elements' => ['arrays' => true]], // 多行数组末尾逗号

        // 大括号
        'no_trailing_comma_in_singleline' => true, // 单行数组无尾逗号
        'no_multiple_statements_per_line' => true, // 每行一条语句

        // 空格和缩进
        'blank_line_after_namespace' => true, // namespace 后空行
        'blank_line_after_opening_tag' => true, // 开标签后空行
        'cast_spaces' => ['space' => 'single'], // 类型转换空格
        'concat_space' => ['spacing' => 'one'], // 连接符空格
        'declare_equal_normalize' => true, // declare 等号空格
        'elseif' => true, // 使用 elseif 而非 else if
        'indentation_type' => true, // 4 空格缩进
        'no_trailing_whitespace_in_comment' => true, // 注释无尾空格
        'no_whitespace_in_blank_line' => true, // 空行无空格
        'return_type_declaration' => ['space_before' => 'none'], // 返回类型声明
        'single_blank_line_before_namespace' => true, // namespace 前空行
        'ternary_operator_spaces' => true, // 三元运算符空格
        'single_space_around_construct' => true, // 结构周围单空格

        // 命名空间和导入
        'no_unused_imports' => true, // 移除未使用导入
        'fully_qualified_strict_types' => true, // 移除冗余完全限定名（已导入时使用短名）
        'ordered_imports' => [
            'sort_algorithm' => 'alpha', // 导入按字母排序
            'imports_order' => ['class', 'function', 'const'], // 导入顺序
        ],

        // 字符串
        'single_quote' => true, // 使用单引号
        'no_binary_string' => true, // 无二进制字符串标记
        'explicit_string_variable' => true, // 明确的字符串变量
        'simple_to_complex_string_variable' => true, // 复杂变量用花括号
        'no_trailing_whitespace' => true, // 无尾随空格

        // 类型声明
        'strict_types' => true, // 严格类型声明
        'declare_strict_types' => true, // declare strict_types

        // 类相关
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'one', // 常量间隔
                'property' => 'one', // 属性间隔
                'method' => 'one', // 方法间隔
            ],
        ],
        'class_definition' => true, // 类定义格式
        'constant_case' => ['case' => 'lower'], // 常量小写
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline', // 多行参数格式
        ],
        'no_blank_lines_after_class_opening' => true, // 类开始无空行

        // 函数相关
        'function_declaration' => true, // 函数声明格式
        'lambda_not_used_import' => true, // 移除未使用闭包导入
        'native_function_casing' => true, // 内置函数大小写
        'native_function_type_declaration_casing' => true, // 内置类型大小写

        // 控制结构
        'switch_case_semicolon_to_colon' => true, // switch case 冒号
        'switch_case_space' => true, // switch case 空格
        'include' => true, // include/require 格式

        // 运算符
        'standardize_not_equals' => true, // 使用 != 而非<>
        'binary_operator_spaces' => [
            'default' => 'single_space', // 二元运算符空格
            'operators' => [
                '=>' => 'single_space', // 数组键值运算符
                '=' => 'single_space', // 赋值运算符
            ],
        ],
        'unary_operator_spaces' => true, // 一元运算符空格

        // 注释
        'no_empty_comment' => true, // 无空注释
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true, // 允许@var mixed
            'allow_unused_params' => false, // 不允许未使用参数
        ],

        // ========== 风险操作（需要理解代码语义） ==========
        // 自增/自减
        'increment_style' => ['style' => 'post'], // 后置++/--

        // 函数调用
        'native_function_invocation' => [
            'scope' => 'namespaced', // 命名空间内使用完全限定名
            'include' => ['@all'],
        ],
    ])
    ->setFinder($finder)
    ->setUsingCache(false);
