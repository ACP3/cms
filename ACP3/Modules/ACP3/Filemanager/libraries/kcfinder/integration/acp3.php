<?php
namespace kcfinder\cms;
use ACP3\Application;

/**
 * Class ACP3
 * @package kcfinder\cms
 */
class ACP3
{
    /**
     * @var bool
     */
    protected static $authenticated = false;

    /**
     * @return bool
     */
    static function checkAuth()
    {
        $currentCwd = getcwd();
        if (!self::$authenticated) {
            define('IN_ACP3', true);
            if (!defined('ACP3_ROOT_DIR')) {
                define('ACP3_ROOT_DIR', realpath(__DIR__ . '/../../../../../../../') . '/');
            }

            require_once ACP3_ROOT_DIR . 'ACP3/Application.php';

            $application = new Application();
            $application->defineDirConstants();
            $application->startupChecks();
            $application->includeAutoLoader();
            $application->initializeClasses();

            chdir(ACP3_ROOT_DIR);

            // if user has access permission...
            if ($application->getContainer()->get('core.auth')->isUser()) {
                if (!isset($_SESSION['KCFINDER'])) {
                    $_SESSION['KCFINDER'] = array();
                    $_SESSION['KCFINDER']['disabled'] = false;
                }

                // User has permission, so make sure KCFinder is not disabled!
                $_SESSION['KCFINDER']['disabled'] = false;

                chdir($currentCwd);

                self::$authenticated = true;
            }
        }

        chdir($currentCwd);
        return self::$authenticated;
    }
}

\kcfinder\cms\ACP3::checkAuth();
?>