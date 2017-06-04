<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractWidgetAction
 * @package ACP3\Core\Controller
 */
abstract class AbstractWidgetAction implements ActionInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserModel
     */
    protected $user;
    /**
     * @var \ACP3\Core\I18n\TranslatorInterface
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    protected $router;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    protected $config;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var Response
     */
    protected $response;
    /**
     * @var ActionResultFactory
     */
    private $actionResultFactory;
    /**
     * @var Core\I18n\Locale
     */
    protected $locale;

    /**
     * WidgetController constructor.
     *
     * @param \ACP3\Core\Controller\Context\WidgetContext $context
     */
    public function __construct(Core\Controller\Context\WidgetContext $context)
    {
        $this->container = $context->getContainer();
        $this->eventDispatcher = $context->getEventDispatcher();
        $this->user = $context->getUser();
        $this->translator = $context->getTranslator();
        $this->locale = $context->getLocale();
        $this->request = $context->getRequest();
        $this->router = $context->getRouter();
        $this->view = $context->getView();
        $this->modules = $context->getModules();
        $this->config = $context->getConfig();
        $this->appPath = $context->getAppPath();
        $this->response = $context->getResponse();
        $this->actionResultFactory = $context->getActionResultFactory();
    }

    /**
     * @return $this
     * @throws Core\ACL\Exception\AccessForbiddenException
     */
    public function preDispatch()
    {
        $this->view->assign([
            'PHP_SELF' => $this->appPath->getPhpSelf(),
            'ROOT_DIR' => $this->appPath->getWebRoot(),
            'HOST_NAME' => $this->request->getHttpHost(),
            'ROOT_DIR_ABSOLUTE' => $this->request->getScheme() . '://' . $this->request->getHttpHost() . $this->appPath->getWebRoot(),
            'DESIGN_PATH' => $this->appPath->getDesignPathWeb(),
            'DESIGN_PATH_ABSOLUTE' => $this->appPath->getDesignPathAbsolute(),
            'LANG_DIRECTION' => $this->locale->getDirection(),
            'LANG' => $this->locale->getShortIsoCode(),
        ]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function postDispatch()
    {
        $this->addCustomTemplateVarsBeforeOutput();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function get(string $serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * @inheritdoc
     */
    public function display($actionResult): Response
    {
        return $this->actionResultFactory->create($actionResult);
    }

    /**
     * @inheritdoc
     */
    protected function addCustomTemplateVarsBeforeOutput()
    {
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getResponse()
    {
        return $this->response;
    }

    /**
     * @return Core\Settings\SettingsInterface
     */
    protected function getSettings()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    protected function getApplicationMode()
    {
        return $this->appPath->getEnvironment();
    }
}
