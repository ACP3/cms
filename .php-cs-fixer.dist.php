<?php

$finder = PhpCsFixer\Finder::create()
    ->notPath('#ACP3/Modules/.*/.*/Resources#')
    ->exclude('build')
    ->exclude('cache')
    ->exclude('designs')
    ->exclude('node_modules')
    ->exclude('uploads')
    ->exclude('vendor')
    ->in(__DIR__);

$header = <<<DOCBLOCK
Copyright (c) by the ACP3 Developers.
See the LICENSE file at the top-level module directory for licensing details.
DOCBLOCK;

$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setLineEnding("\n")
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'class_attributes_separation' => ['elements' => ['method' => 'one']],
        'concat_space' => ['spacing' => 'one'],
        'declare_equal_normalize' => ['space' => 'single'],
        'header_comment' => [
            'comment_type' => 'PHPDoc',
            'header' => $header,
        ],
        'method_chaining_indentation' => true,
        'modernize_types_casting' => true,
        'native_function_invocation' => true,
        'no_null_property_initialization' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_imports' => ['imports_order' => null],
        'ternary_to_null_coalescing' => true,
        'yoda_style' => false,
    ])
    ->setFinder($finder)
;
