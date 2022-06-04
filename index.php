<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

use ACP3\Core\Application\Bootstrap;
use ACP3\Core\Environment\ApplicationMode;
use Symfony\Component\HttpFoundation\Request;

\define('ACP3_ROOT_DIR', realpath(__DIR__));

require ACP3_ROOT_DIR . '/vendor/autoload.php';

$request = Request::createFromGlobals();

$appMode = ApplicationMode::PRODUCTION;
if (getenv('ACP3_APPLICATION_MODE') === ApplicationMode::DEVELOPMENT->value) {
    $appMode = ApplicationMode::DEVELOPMENT;
}

$kernel = new Bootstrap($appMode);

if (!$kernel->isInstalled()) {
    echo <<<HTML
The ACP3 is not correctly installed.
Please navigate to the <a href="{$request->getBasePath()}/installation/">installation wizard</a>
and follow its instructions.
HTML;
    exit;
}

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
