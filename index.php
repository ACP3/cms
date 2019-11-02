<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

use ACP3\Core\Application\Bootstrap;
use ACP3\Core\Application\BootstrapCache;
use ACP3\Core\Application\BootstrapCache\Esi;
use ACP3\Core\Environment\ApplicationMode;
use Symfony\Component\HttpFoundation\Request;
use Toflar\Psr6HttpCacheStore\Psr6Store;

\define('ACP3_ROOT_DIR', \realpath(__DIR__));

require ACP3_ROOT_DIR . '/vendor/autoload.php';

$request = Request::createFromGlobals();

$appMode = ApplicationMode::PRODUCTION;
if (\getenv('ACP3_APPLICATION_MODE') === ApplicationMode::DEVELOPMENT) {
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

$cacheStore = new Psr6Store([
    'cache_directory' => __DIR__ . '/cache/' . $appMode . '/http',
]);

$appCache = new BootstrapCache(
    $kernel,
    $cacheStore,
    new Esi(),
    ['debug' => $appMode === ApplicationMode::DEVELOPMENT]
);

$appCache->handle($request)->send();
