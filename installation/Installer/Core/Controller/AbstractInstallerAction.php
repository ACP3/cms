<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\Controller;

use ACP3\Core\Controller\ActionInterface;
use ACP3\Core\Controller\LayoutAwareControllerTrait;
use ACP3\Core\Http\RedirectResponse;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\ExtractFromPathTrait;
use ACP3\Core\View;
use Fisharebest\Localization\Locale;
use Symfony\Component\HttpFoundation\Response;

/**
 * Module Controller of the installer modules
 * @package ACP3\Installer\Core\Controller
 */
abstract class AbstractInstallerAction implements ActionInterface
{
    use ExtractFromPathTrait;
    use LayoutAwareControllerTrait;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;
    /**
     * @var \ACP3\Core\I18n\TranslatorInterface
     */
    protected $translator;
    /**
     * @var \ACP3\Core\I18n\LocaleInterface
     */
    protected $locale;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    protected $router;
    /**
     * @var \ACP3\Installer\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;
    /**
     * @var \ACP3\Core\Controller\ActionResultFactory
     */
    private $actionResultFactory;

    /**
     * @param \ACP3\Installer\Core\Controller\Context\InstallerContext $context
     */
    public function __construct(Context\InstallerContext $context)
    {
        $this->container = $context->getContainer();
        $this->translator = $context->getTranslator();
        $this->locale = $context->getLocale();
        $this->request = $context->getRequest();
        $this->router = $context->getRouter();
        $this->view = $context->getView();
        $this->response = $context->getResponse();
        $this->appPath = $context->getAppPath();
        $this->actionResultFactory = $context->getActionResultFactory();
    }

    /**
     * @inheritdoc
     */
    public function preDispatch()
    {
        $this->view->assign('LANGUAGES', $this->languagesDropdown($this->locale->getLocale()));
        $this->view->assign('PHP_SELF', $this->appPath->getPhpSelf());
        $this->view->assign('REQUEST_URI', $this->request->getServer()->get('REQUEST_URI'));
        $this->view->assign('ROOT_DIR', $this->appPath->getWebRoot());
        $this->view->assign('INSTALLER_ROOT_DIR', $this->appPath->getInstallerWebRoot());
        $this->view->assign('DESIGN_PATH', $this->appPath->getDesignPathWeb());
        $this->view->assign('UA_IS_MOBILE', $this->request->getUserAgent()->isMobileBrowser());
        $this->view->assign('IS_AJAX', $this->request->isXmlHttpRequest());
        $this->view->assign('LANG_DIRECTION', $this->locale->getDirection());
        $this->view->assign('LANG', $this->locale->getShortIsoCode());
    }

    /**
     * @inheritdoc
     */
    public function postDispatch()
    {
        $this->addCustomTemplateVarsBeforeOutput();
    }

    /**
     * @inheritdoc
     */
    public function display($actionResult): Response
    {
        return $this->actionResultFactory->create($actionResult);
    }

    /**
     * @return RedirectResponse
     */
    public function redirect()
    {
        return $this->get('core.http.redirect_response');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getResponse()
    {
        return $this->response;
    }

    /**
     * @inheritdoc
     */
    protected function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @inheritdoc
     */
    protected function getView(): View
    {
        return $this->view;
    }

    /**
     * @inheritdoc
     */
    public function get(string $serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * Generiert das Dropdown-Menü mit den zur Verfügung stehenden Installersprachen
     *
     * @param string $selectedLanguage
     *
     * @return array
     */
    private function languagesDropdown(string $selectedLanguage): array
    {
        $languages = [];
        $paths = glob($this->appPath->getInstallerModulesDir() . 'Install/Resources/i18n/*.xml');

        foreach ($paths as $file) {
            try {
                $isoCode = $this->getLanguagePackIsoCode($file);
                $locale = Locale::create($isoCode);

                $languages[] = [
                    'language' => $isoCode,
                    'selected' => $selectedLanguage === $isoCode ? ' selected="selected"' : '',
                    'name' => $locale->endonym()
                ];
            } catch (\DomainException $e) {
            }
        }
        return $languages;
    }

    /**
     * @inheritdoc
     */
    protected function addCustomTemplateVarsBeforeOutput()
    {
        $this->view->assign('PAGE_TITLE', $this->translator->t('install', 'acp3_installation'));
        $this->view->assign(
            'TITLE',
            $this->translator->t(
            $this->request->getModule(),
            $this->request->getController() . '_' . $this->request->getAction()
        )
        );
        $this->view->assign('LAYOUT', $this->fetchLayoutViaInheritance('layout.ajax.tpl'));
    }
}
