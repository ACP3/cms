<?php
/**
 * Index
 *
 * @author Tino Goratsch
 */

define('ACP3_ROOT_DIR', realpath(__DIR__) . '/');

require './vendor/autoload.php';

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

$appMode = \ACP3\Core\Environment\ApplicationMode::PRODUCTION;
if (getenv('ACP3_APPLICATION_MODE') === \ACP3\Core\Environment\ApplicationMode::DEVELOPMENT) {
    $appMode = \ACP3\Core\Environment\ApplicationMode::DEVELOPMENT;
}

$kernel = new \ACP3\Core\Application\Bootstrap($appMode);

if (!$kernel->startupChecks()) {
    echo 'The ACP3 is not correctly installed. Please navigate to the <a href="' . $request->getBasePath() . 'installation/">installation wizard</a> and follow its instructions.';
    exit;
}

$kernel
    ->handle($request)
    ->send();
