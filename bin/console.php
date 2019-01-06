<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

\define('ACP3_ROOT_DIR', \realpath(__DIR__ . '/../') . '/');

require ACP3_ROOT_DIR . 'vendor/autoload.php';

$app = new \ACP3\Core\Console\Application(\ACP3\Core\Environment\ApplicationMode::CLI);
$app->run();
