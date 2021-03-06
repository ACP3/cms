<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Resources')
    ->exclude('Tests')
    ->notPath('#ACP3/Modules/.*/.*/Resources#')
    ->in($dir = __DIR__ . '/ACP3');

return new Sami($iterator, array(
    'title' => 'ACP3 CMS API',
    'build_dir' => __DIR__ . '/build/sami/docs',
    'cache_dir' => __DIR__ . '/build/sami/cache',
    'default_opened_level' => 2,
));
