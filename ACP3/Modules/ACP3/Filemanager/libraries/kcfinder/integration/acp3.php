<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace kcfinder\cms;

use ACP3\Core\Application\Bootstrap;

/**
 * Class ACP3
 * @package kcfinder\cms
 */
class acp3
{
    /**
     * @var bool
     */
    protected static $authenticated = false;

    /**
     * @return bool
     */
    public static function checkAuth()
    {
        $currentCwd = getcwd();
        if (!self::$authenticated) {
            if (!defined('ACP3_ROOT_DIR')) {
                define('ACP3_ROOT_DIR', realpath(__DIR__ . '/../../../../../../../') . '/');
            }

            require_once ACP3_ROOT_DIR . 'vendor/autoload.php';

            $application = new Bootstrap();
            if ($application->startupChecks()) {
                $application->initializeClasses();

                chdir(ACP3_ROOT_DIR);

                $application->getContainer()->get('core.user')->authenticate();

                // if user has access permission...
                if ($application->getContainer()->get('core.user')->isAuthenticated()) {
                    if (!isset($_SESSION['KCFINDER'])) {
                        $_SESSION['KCFINDER'] = [];
                        $_SESSION['KCFINDER']['disabled'] = false;
                    }

                    // User has permission, so make sure KCFinder is not disabled!
                    $_SESSION['KCFINDER']['disabled'] = false;

                    chdir($currentCwd);

                    self::$authenticated = true;
                }
            }
        }

        chdir($currentCwd);
        return self::$authenticated;
    }
}

\kcfinder\cms\ACP3::checkAuth();
