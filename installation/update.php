<?php
/**
 * UPDATER
 *
 * @author Tino Goratsch
 */

define('ACP3_ROOT_DIR', realpath(__DIR__ . '/../') . '/');

require '../vendor/autoload.php';

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$kernel = new \ACP3\Installer\Core\Application\Bootstrap(\ACP3\Core\Environment\ApplicationMode::UPDATER);

if (!$kernel->startupChecks()) {
    echo 'The ACP3 is not correctly installed. Please navigate to the <a href="' . $request->getBasePath() . 'installation/">installation wizard</a> and follow its instructions.';
    exit;
}

$kernel
    ->handle($request)
    ->send();
