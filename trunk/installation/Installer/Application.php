<?php

namespace ACP3\Installer;

use ACP3\Core;
use ACP3\Installer\Core\FrontController;
use Doctrine\DBAL;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Class Application
 * @package ACP3\Installer
 */
class Application
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * run() method of the installer
     */
    public function runInstaller()
    {
        $this->defineDirConstants();
        $this->includeAutoLoader();
        $this->initializeInstallerClasses();
        $this->outputPage();
    }

    /**
     * Einige Pfadkonstanten definieren
     */
    public function defineDirConstants()
    {
        define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
        $php_self = dirname(PHP_SELF);
        define('ROOT_DIR', substr($php_self !== '/' ? $php_self . '/' : '/', 0, -13));
        define('INSTALLER_ROOT_DIR', $php_self !== '/' ? $php_self . '/' : '/');
        define('ACP3_DIR', ACP3_ROOT_DIR . 'ACP3/');
        define('CLASSES_DIR', ACP3_DIR . 'Core/');
        define('MODULES_DIR', ACP3_DIR . 'Modules/');
        define('LIBRARIES_DIR', ACP3_ROOT_DIR . 'libraries/');
        define('VENDOR_DIR', ACP3_ROOT_DIR . 'vendor/');
        define('UPLOADS_DIR', ACP3_ROOT_DIR . 'uploads/');
        define('CACHE_DIR', UPLOADS_DIR . 'cache/');

        define('INSTALLER_ACP3_DIR', realpath(ACP3_DIR . '../installation/') . '/Installer/');
        define('INSTALLER_MODULES_DIR', INSTALLER_ACP3_DIR . 'Modules/');
        define('INSTALLER_CLASSES_DIR', INSTALLER_ACP3_DIR . 'Core/');
        define('INSTALLATION_DIR', ACP3_ROOT_DIR . 'installation/');

        // Pfade zum Theme setzen
        define('DESIGN_PATH', INSTALLATION_DIR . 'design/');
        define('DESIGN_PATH_INTERNAL', INSTALLATION_DIR . 'design/');
    }

    /**
     * Klassen Autoloader inkludieren
     */
    public function includeAutoLoader()
    {
        require VENDOR_DIR . 'autoload.php';
    }

    /**
     * Initialisieren der Klassen für den Installer
     */
    public function initializeInstallerClasses()
    {
        $this->container = new ContainerBuilder();
        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__));
        $loader->load(ACP3_DIR . 'config/services.yml');
        $loader->load(INSTALLER_ACP3_DIR . 'config/overridden.yml');
        $loader->load(INSTALLER_ACP3_DIR . 'config/services.yml');
        $loader->load(INSTALLER_CLASSES_DIR . 'View/Renderer/Smarty/plugins.yml');

        // Load installer modules services
        $modules = array_diff(scandir(INSTALLER_MODULES_DIR), array('.', '..'));
        foreach ($modules as $module) {
            $path = INSTALLER_MODULES_DIR . $module . '/config/services.yml';
            if (is_file($path) === true) {
                $loader->load($path);
            }
        }

        $params = array(
            'compile_id' => 'installer',
            'plugins_dir' => INSTALLER_CLASSES_DIR . 'View/Renderer/Smarty/',
            'template_dir' => array(DESIGN_PATH_INTERNAL, INSTALLER_MODULES_DIR)
        );
        $this->container->get('core.view')->setRenderer('smarty', $params);

        $this->container->compile();
    }

    /**
     * Gibt die Seite aus
     */
    public function outputPage()
    {
        $request = $this->container->get('core.request');

        $frontController = new FrontController($this->container);
        $errorsServiceId = 'errors.controller.install.index';

        try {
            $serviceId = $request->mod . '.controller.install.' . $request->controller;
            $frontController->dispatch($serviceId, $request->file);
        } catch (Core\Exceptions\ControllerActionNotFound $e) {
            $frontController->dispatch($errorsServiceId, '404');
        } catch (\Exception $e) {
            $frontController->dispatch($errorsServiceId, '404');
        }
    }

    /**
     * run() method of the database updater
     */
    public function runUpdater()
    {
        $this->defineDirConstants();
        $this->startupChecks();
        $this->includeAutoLoader();
        $this->initializeUpdaterClasses();
        $this->outputPage();
    }

    /**
     * Überprüft, ob die config.php existiert
     */
    public function startupChecks()
    {
        // Standardzeitzone festlegen
        date_default_timezone_set('UTC');

        error_reporting(E_ALL);

        if (defined('IN_UPDATER') === true) {
            // DB-Config des ACP3 laden
            $path = ACP3_DIR . 'config/config.php';
            if (is_file($path) === false || filesize($path) === 0) {
                exit('The ACP3 is not correctly installed. Please navigate to the <a href="' . ROOT_DIR . 'installation/">installation wizard</a> and follow its instructions.');
                // Wenn alles okay ist, config.php einbinden und error_reporting setzen
            } else {
                require_once $path;
            }
        }
    }

    /**
     * Initialisieren der Klassen für den Updater
     */
    public function initializeUpdaterClasses()
    {
        $config = new DBAL\Configuration();
        $connectionParams = array(
            'dbname' => CONFIG_DB_NAME,
            'user' => CONFIG_DB_USER,
            'password' => CONFIG_DB_PASSWORD,
            'host' => CONFIG_DB_HOST,
            'driver' => 'pdo_mysql',
            'charset' => 'utf8'
        );
        $db = DBAL\DriverManager::getConnection($connectionParams, $config);

        define('DB_PRE', CONFIG_DB_PRE);

        $this->container = new ContainerBuilder();

        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__));
        $loader->load(ACP3_DIR . 'config/services.yml');
        $loader->load(INSTALLER_ACP3_DIR . 'config/services.yml');
        $loader->load(INSTALLER_ACP3_DIR . 'config/update.yml');
        $loader->load(INSTALLER_CLASSES_DIR . 'View/Renderer/Smarty/plugins.yml');

        // Load installer modules services
        $installerModules = array_diff(scandir(INSTALLER_MODULES_DIR), array('.', '..'));
        foreach ($installerModules as $module) {
            $path = INSTALLER_MODULES_DIR . $module . '/config/services.yml';
            if (is_file($path) === true) {
                $loader->load($path);
            }
        }

        $modules = array_diff(scandir(MODULES_DIR), array('.', '..'));
        foreach ($modules as $module) {
            $path = MODULES_DIR . $module . '/config/services.yml';
            if (is_file($path) === true) {
                $loader->load($path);
            }
        }

        $this->container->set('core.db', $db);

        // Systemeinstellungen laden
        $this->container
            ->get('system.config')
            ->getSettingsAsConstants();

        $params = array(
            'compile_id' => 'installer',
            'plugins_dir' => INSTALLER_CLASSES_DIR . 'View/Renderer/Smarty/',
            'template_dir' => array(DESIGN_PATH_INTERNAL, INSTALLER_MODULES_DIR)
        );
        $this->container->get('core.view')->setRenderer('smarty', $params);

        $this->container->compile();
    }

    /**
     * @return ContainerBuilder
     */
    public function getServiceContainer()
    {
        return $this->container;
    }

}