<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\Controller;

use ACP3\Core\Controller\ActionInterface;
use ACP3\Core\Controller\DisplayActionTrait;
use ACP3\Core\Http\RedirectResponse;

/**
 * Module Controller of the installer modules.
 */
abstract class AbstractInstallerAction implements ActionInterface
{
    use DisplayActionTrait;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
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
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;
    /**
     * @var string
     */
    private $layout = 'layout.tpl';

    /**
     * @param \ACP3\Installer\Core\Controller\Context\InstallerContext $context
     */
    public function __construct(Context\InstallerContext $context)
    {
        $this->container = $context->getContainer();
        $this->translator = $context->getTranslator();
        $this->request = $context->getRequest();
        $this->router = $context->getRouter();
        $this->view = $context->getView();
        $this->response = $context->getResponse();
        $this->appPath = $context->getAppPath();
        $this->redirectResponse = $context->getRedirectResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function preDispatch()
    {
    }

    /**
     * @return RedirectResponse
     */
    public function redirect()
    {
        return $this->redirectResponse;
    }

    /**
     * {@inheritdoc}
     */
    protected function getResponse()
    {
        return $this->response;
    }

    /**
     * @return \ACP3\Core\View
     */
    protected function getView()
    {
        return $this->view;
    }

    /**
     * @return bool
     */
    protected function getNoOutput()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function get($serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyTemplateAutomatically()
    {
        return $this->request->getModule() . '/' . $this->request->getController() . '.' . $this->request->getAction() . '.tpl';
    }

    /**
     * {@inheritdoc}
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
        $this->view->assign('LAYOUT', $this->request->isXmlHttpRequest() ? 'layout.ajax.tpl' : $this->getLayout());
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     *
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;

        return $this;
    }
}
