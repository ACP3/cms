<?php

namespace ACP3\Modules\System\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Permissions;
use ACP3\Modules\System;

/**
 * Class Extensions
 * @package ACP3\Modules\System\Controller\Admin
 */
class Extensions extends Core\Modules\Controller\Admin
{
    /**
     * @var Core\XML
     */
    protected $xml;
    /**
     * @var System\Model
     */
    protected $systemModel;
    /**
     * @var Core\Config
     */
    protected $systemConfig;
    /**
     * @var \ACP3\Modules\Permissions\Cache
     */
    protected $permissionsCache;

    /**
     * @param Core\Context\Admin $context
     * @param Core\XML $xml
     * @param System\Model $systemModel
     * @param Core\Config $systemConfig
     * @param Permissions\Cache $permissionsCache
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\XML $xml,
        System\Model $systemModel,
        Core\Config $systemConfig,
        Permissions\Cache $permissionsCache)
    {
        parent::__construct($context);

        $this->xml = $xml;
        $this->systemModel = $systemModel;
        $this->systemConfig = $systemConfig;
        $this->permissionsCache = $permissionsCache;
    }

    public function actionDesigns()
    {
        if (isset($this->request->dir)) {
            $this->_designsPost($this->request->dir);
        } else {
            $designs = [];
            $path = ACP3_ROOT_DIR . 'designs/';
            $directories = scandir($path);
            $count_dir = count($directories);
            for ($i = 0; $i < $count_dir; ++$i) {
                $designInfo = $this->xml->parseXmlFile($path . $directories[$i] . '/info.xml', '/design');
                if (!empty($designInfo)) {
                    $designs[$i] = $designInfo;
                    $designs[$i]['selected'] = $this->systemConfig->getSettings()['design'] === $directories[$i] ? 1 : 0;
                    $designs[$i]['dir'] = $directories[$i];
                }
            }
            $this->view->assign('designs', $designs);
        }
    }

    /**
     * @param $design
     */
    protected function _designsPost($design)
    {
        $bool = false;

        if ((bool)preg_match('=/=', $design) === false &&
            is_file(ACP3_ROOT_DIR . 'designs/' . $design . '/info.xml') === true
        ) {
            $bool = $this->systemConfig->setSettings(['design' => $design]);

            // Template Cache leeren
            Core\Cache::purge(UPLOADS_DIR . 'cache/tpl_compiled');
            Core\Cache::purge(UPLOADS_DIR . 'cache/tpl_cached');
        }

        $text = $this->lang->t('system', $bool === true ? 'designs_edit_success' : 'designs_edit_error');

        $this->redirectMessages()->setMessage($bool, $text, 'acp/system/extensions/designs');
    }

    public function actionIndex()
    {
        return;
    }

    public function actionModules()
    {
        switch ($this->request->action) {
            case 'activate':
                $this->_enableModule();
                break;
            case 'deactivate':
                $this->_disableModule();
                break;
            case 'install':
                $this->_installModule();
                break;
            case 'uninstall':
                $this->_uninstallModule();
                break;
            default:
                $this->_renewCaches();

                $modules = $this->modules->getAllModules();
                $installedModules = $newModules = [];

                foreach ($modules as $key => $values) {
                    $values['dir'] = strtolower($values['dir']);
                    if ($this->modules->isInstalled($values['dir']) === true) {
                        $installedModules[$key] = $values;
                    } else {
                        $newModules[$key] = $values;
                    }
                }

                $this->view->assign('installed_modules', $installedModules);
                $this->view->assign('new_modules', $newModules);
        }
    }

    protected function _enableModule()
    {
        $bool = false;
        $info = $this->modules->getModuleInfo($this->request->dir);
        if (empty($info)) {
            $text = $this->lang->t('system', 'module_not_found');
        } elseif ($info['protected'] === true) {
            $text = $this->lang->t('system', 'mod_deactivate_forbidden');
        } else {
            $bool = $this->systemModel->update(['active' => 1], ['name' => $this->request->dir]);

            $this->_renewCaches();

            $text = $this->lang->t('system', 'mod_activate_' . ($bool !== false ? 'success' : 'error'));
        }

        $this->redirectMessages()->setMessage($bool, $text, 'acp/system/extensions/modules');
    }

    protected function _renewCaches()
    {
        $this->lang->setLanguageCache();
        $this->modules->setModulesCache();

        $this->permissionsCache->setResourcesCache();
    }

    protected function _disableModule()
    {
        $bool = false;
        $info = $this->modules->getModuleInfo($this->request->dir);
        if (empty($info)) {
            $text = $this->lang->t('system', 'module_not_found');
        } elseif ($info['protected'] === true) {
            $text = $this->lang->t('system', 'mod_deactivate_forbidden');
        } else {
            $serviceId = strtolower($this->request->dir . '.installer');

            if ($this->container->has($serviceId) === true) {
                $installer = $this->get($serviceId);

                // Modulabhängigkeiten prüfen
                $deps = $this->get('system.helpers')->checkUninstallDependencies($installer);

                if (empty($deps)) {
                    $bool = $this->systemModel->update(['active' => 0], ['name' => $this->request->dir]);

                    $this->_renewCaches();

                    $text = $this->lang->t('system', 'mod_deactivate_' . ($bool !== false ? 'success' : 'error'));
                } else {
                    $text = sprintf($this->lang->t('system', 'module_disable_not_possible'), implode(', ', $deps));
                }
            } else {
                throw new Core\Exceptions\ResultNotExists();
            }
        }

        $this->redirectMessages()->setMessage($bool, $text, 'acp/system/extensions/modules');
    }

    protected function _installModule()
    {
        $bool = false;
        // Nur noch nicht installierte Module berücksichtigen
        if ($this->modules->isInstalled($this->request->dir) === false) {
            $serviceId = strtolower($this->request->dir . '.installer');

            if ($this->container->has($serviceId) === true) {
                $installer = $this->get($serviceId);

                // Modulabhängigkeiten prüfen
                $deps = $this->get('system.helpers')->checkInstallDependencies($installer);

                // Modul installieren
                if (empty($deps)) {
                    $bool = $installer->install();

                    $this->_renewCaches();

                    $text = $this->lang->t('system', 'mod_installation_' . ($bool !== false ? 'success' : 'error'));
                } else {
                    $text = sprintf($this->lang->t('system', 'enable_following_modules_first'), implode(', ', $deps));
                }
            } else {
                $text = $this->lang->t('system', 'module_installer_not_found');
            }
        } else {
            $text = $this->lang->t('system', 'module_already_installed');
        }

        $this->redirectMessages()->setMessage($bool, $text, 'acp/system/extensions/modules');
    }

    protected function _uninstallModule()
    {
        $bool = false;
        $info = $this->modules->getModuleInfo($this->request->dir);
        // Nur installierte und Nicht-Core-Module berücksichtigen
        if ($info['protected'] === false && $this->modules->isInstalled($this->request->dir) === true) {
            $serviceId = strtolower($this->request->dir . '.installer');

            if ($this->container->has($serviceId) === true) {
                $installer = $this->get($serviceId);

                // Modulabhängigkeiten prüfen
                $deps = $this->get('system.helpers')->checkUninstallDependencies($installer);

                // Modul deinstallieren
                if (empty($deps)) {
                    $bool = $installer->uninstall();

                    $this->_renewCaches();

                    $text = $this->lang->t('system', 'mod_uninstallation_' . ($bool !== false ? 'success' : 'error'));
                } else {
                    $text = sprintf($this->lang->t('system', 'uninstall_following_modules_first'), implode(', ', $deps));
                }
            } else {
                $text = $this->lang->t('system', 'module_installer_not_found');
            }
        } else {
            $text = $this->lang->t('system', 'protected_module_description');
        }

        $this->redirectMessages()->setMessage($bool, $text, 'acp/system/extensions/modules');
    }

}