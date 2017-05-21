<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

define('ACP3_ROOT_DIR', realpath(__DIR__ . '/../') . '/');

require ACP3_ROOT_DIR . 'vendor/autoload.php';

$app = new \ACP3\Core\Console\Application('console');
$app->run();
