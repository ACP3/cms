<?php
/**
 * Installer
 *
 * @author Tino Goratsch
 */

define('ACP3_ROOT_DIR', realpath(__DIR__ . '/../') . '/');

require '../vendor/autoload.php';

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$kernel = new \ACP3\Installer\Core\Application\Bootstrap(\ACP3\Core\Environment\ApplicationMode::INSTALLER);

$kernel
    ->handle($request)
    ->send();
