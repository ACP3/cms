<?php
namespace kcfinder\cms;

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
                define('ACP3_ROOT_DIR', dirname(__DIR__ . '/../../../../') . '/');
            }

            require_once ACP3_ROOT_DIR . 'ACP3/Application.php';

            \ACP3\Application::defineDirConstants();
            \ACP3\Application::startupChecks();
            \ACP3\Application::includeAutoLoader();
            \ACP3\Application::initializeClasses();

            chdir(ACP3_ROOT_DIR);

            // if user has access permission...
            if (\ACP3\Core\Registry::get('Auth')->isUser()) {
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