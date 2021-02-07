<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Modules\ACP3\Installer\Core\Application\Bootstrap;
use Symfony\Component\HttpFoundation\Request;

\define('ACP3_ROOT_DIR', \dirname(__DIR__));

require ACP3_ROOT_DIR . '/vendor/autoload.php';

$request = Request::createFromGlobals();
$kernel = new Bootstrap(ApplicationMode::UPDATER);

if (!$kernel->isInstalled()) {
    echo <<<HTML
The ACP3 is not correctly installed.
Please navigate to the <a href="{$request->getBasePath()}/">installation wizard</a>
and follow its instructions.
HTML;
    exit;
}

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
