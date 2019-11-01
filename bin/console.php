<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

use ACP3\Core\Console\Application;
use ACP3\Core\Environment\ApplicationMode;

\define('ACP3_ROOT_DIR', \dirname(__DIR__));

require ACP3_ROOT_DIR . '/vendor/autoload.php';

$app = new Application(ApplicationMode::CLI);
$app->run();
