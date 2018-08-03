<?php
namespace kcfinder\cms;

use ACP3\Core\Application\Bootstrap;
use ACP3\Core\Environment\ApplicationMode;
use Symfony\Component\HttpFoundation\Request;


class acp3
{
    /**
     * @var bool
     */
    protected static $authenticated = false;

    /**
     * @return bool
     * @throws \Exception
     */
    public static function checkAuth()
    {
        $currentCwd = getcwd();
        if (!self::$authenticated) {
            if (!defined('ACP3_ROOT_DIR')) {
                define('ACP3_ROOT_DIR', realpath(__DIR__ . '/../../../../../../../') . '/');
            }

            require_once ACP3_ROOT_DIR . 'vendor/autoload.php';

            $appMode = ApplicationMode::PRODUCTION;
            if (\getenv('ACP3_APPLICATION_MODE') === ApplicationMode::DEVELOPMENT) {
                $appMode = ApplicationMode::DEVELOPMENT;
            }

            $application = new Bootstrap($appMode);
            if ($application->startupChecks()) {
                $application->setErrorHandler();
                $symfonyRequest = Request::createFromGlobals();
                $application->initializeClasses($symfonyRequest);

                chdir(ACP3_ROOT_DIR);

                if ($application->getContainer()->get('filemanager.helpers.kcfinder_authentication_helper')->checkAuthorization()) {
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

\kcfinder\cms\acp3::checkAuth();
