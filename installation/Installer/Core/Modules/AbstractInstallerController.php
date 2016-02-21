<?php

namespace ACP3\Installer\Core\Modules;

use ACP3\Core\Filesystem;
use ACP3\Core\Modules\ControllerInterface;
use ACP3\Core\Modules\DisplayControllerActionTrait;
use ACP3\Core\Redirect;
use ACP3\Installer\Core\Modules\Controller\InstallerContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Module Controller of the installer modules
 * @package ACP3\Installer\Core\Modules
 */
abstract class AbstractInstallerController implements ControllerInterface
{
    use DisplayControllerActionTrait;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var \ACP3\Installer\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Installer\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Installer\Core\Environment\ApplicationPath
     */
    protected $appPath;

    /**
     * @param \ACP3\Installer\Core\Modules\Controller\InstallerContext $context
     */
    public function __construct(InstallerContext $context)
    {
        $this->translator = $context->getTranslator();
        $this->request = $context->getRequest();
        $this->router = $context->getRouter();
        $this->view = $context->getView();
        $this->response = $context->getResponse();
        $this->appPath = $context->getAppPath();
    }

    /**
     * @inheritdoc
     */
    public function preDispatch()
    {
        if ($this->request->getPost()->has('lang')) {
            setcookie('ACP3_INSTALLER_LANG', $this->request->getPost()->get('lang', ''), time() + 3600, '/');
            $this->redirect()->temporary($this->request->getFullPath());
        }

        $this->setLanguage();

        // Einige Template Variablen setzen
        $this->view->assign('LANGUAGES', $this->languagesDropdown($this->translator->getLocale()));
        $this->view->assign('PHP_SELF', $this->appPath->getPhpSelf());
        $this->view->assign('REQUEST_URI', $this->request->getServer()->get('REQUEST_URI'));
        $this->view->assign('ROOT_DIR', $this->appPath->getWebRoot());
        $this->view->assign('INSTALLER_ROOT_DIR', $this->appPath->getInstallerWebRoot());
        $this->view->assign('DESIGN_PATH', $this->appPath->getDesignPathWeb());
        $this->view->assign('UA_IS_MOBILE', $this->request->getUserAgent()->isMobileBrowser());
        $this->view->assign('IS_AJAX', $this->request->isAjax());

        $languageInfo = simplexml_load_file($this->appPath->getInstallerModulesDir() . 'Install/Resources/i18n/' . $this->translator->getLocale() . '.xml');
        $this->view->assign('LANG_DIRECTION',
            isset($languageInfo->info->direction) ? $languageInfo->info->direction : 'ltr');
        $this->view->assign('LANG', $this->translator->getShortIsoCode());
    }

    /**
     * @return Redirect
     */
    public function redirect()
    {
        return $this->get('core.redirect');
    }

    /**
     * @inheritdoc
     */
    public function get($serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * Generiert das Dropdown-Menü mit der zur Verfügung stehenden Installersprachen
     *
     * @param string $selectedLanguage
     *
     * @return array
     */
    private function languagesDropdown($selectedLanguage)
    {
        // Dropdown-Menü für die Sprachen
        $languages = [];
        $path = $this->appPath->getInstallerModulesDir() . 'Install/Resources/i18n/';

        foreach (Filesystem::scandir($path) as $row) {
            $langInfo = simplexml_load_file($path . $row);
            if (!empty($langInfo)) {
                $languages[] = [
                    'language' => substr($row, 0, -4),
                    'selected' => $selectedLanguage === substr($row, 0, -4) ? ' selected="selected"' : '',
                    'name' => $langInfo->info->name
                ];
            }
        }
        return $languages;
    }

    /**
     * @inheritdoc
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function applyTemplateAutomatically()
    {
        return $this->request->getModule() . '/' . $this->request->getController() . '.' . $this->request->getControllerAction() . '.tpl';
    }

    protected function addCustomTemplateVarsBeforeOutput()
    {
        $this->view->assign('PAGE_TITLE', $this->translator->t('install', 'acp3_installation'));
        $this->view->assign('TITLE', $this->translator->t(
            $this->request->getModule(),
            $this->request->getController() . '_' . $this->request->getControllerAction())
        );
    }

    private function setLanguage()
    {
        $cookieLocale = $this->request->getCookies()->get('ACP3_INSTALLER_LANG', '');
        if (!preg_match('=/=', $cookieLocale) &&
            is_file($this->appPath->getInstallerModulesDir() . 'Install/Resources/i18n/' . $cookieLocale . '.xml') === true
        ) {
            $language = $cookieLocale;
        } else {
            $language = 'en_US'; // Fallback default language

            foreach ($this->request->getUserAgent()->parseAcceptLanguage() as $locale => $val) {
                $locale = str_replace('-', '_', $locale);
                if ($this->translator->languagePackExists($locale) === true) {
                    $language = $locale;
                    break;
                }
            }
        }

        $this->translator->setLocale($language);
    }
}
