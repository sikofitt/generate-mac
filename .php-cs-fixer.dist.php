<?php declare(strict_types=1);

/*
 * Copyright (c) 2018  https://sikofitt.com sikofitt@sikofitt.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use PhpCsFixer\Config;

$header = '';

if (file_exists(__DIR__.'/header.txt')) {
    $header = file_get_contents('header.txt');
}
return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@PHP82Migration' => true,
        '@PHP82Migration:risky' => true,
        'header_comment' => ['header' => $header],
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'no_mixed_echo_print' => ['use' => 'print'],
        'strict_param' => true,
        'strict_comparison' => true,
        'single_import_per_statement' => true,
        'phpdoc_order' => true,
        'array_syntax' => ['syntax' => 'short'],
        'phpdoc_add_missing_param_annotation' => true,
        'psr_autoloading' => true,
        'phpdoc_var_without_name' => false,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'break',
                'continue',
                'extra',
                'return',
                'throw',
                'parenthesis_brace_block',
                'square_brace_block',
                'curly_brace_block',
            ],
        ],
    ])->setFinder(
        PhpCsFixer\Finder::create()
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->name('*.php')
            ->in([
                'src',
                'tests',
                'bin',
            ])
    );
