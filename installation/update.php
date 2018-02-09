<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */
\define('ACP3_ROOT_DIR', \realpath(__DIR__ . '/../') . '/');

require ACP3_ROOT_DIR . 'vendor/autoload.php';

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$kernel = new \ACP3\Installer\Core\Application\Bootstrap(\ACP3\Core\Environment\ApplicationMode::UPDATER);

if (!$kernel->startupChecks()) {
    echo <<<HTML
The ACP3 is not correctly installed.
Please navigate to the <a href="{$request->getBasePath()}/">installation wizard</a>
and follow its instructions.
HTML;
    exit;
}

$kernel
    ->handle($request)
    ->send();
