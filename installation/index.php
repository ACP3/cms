<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Installer\Core\Application\Bootstrap;
use Symfony\Component\HttpFoundation\Request;

\define('ACP3_ROOT_DIR', \dirname(__DIR__) . '/');

require ACP3_ROOT_DIR . 'vendor/autoload.php';

$request = Request::createFromGlobals();
$kernel = new Bootstrap(ApplicationMode::INSTALLER);

$kernel
    ->handle($request)
    ->send();
