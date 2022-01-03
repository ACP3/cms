<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractWidgetAction implements InvokableActionInterface
{
    protected Translator $translator;
    protected RequestInterface $request;
    protected View $view;
    protected SettingsInterface $config;
    private string $applicationMode;

    private string $template = '';
    private string|false $content = '';

    public function __construct(WidgetContext $context)
    {
        $this->translator = $context->getTranslator();
        $this->request = $context->getRequest();
        $this->view = $context->getView();
        $this->config = $context->getConfig();
        $this->applicationMode = $context->getApplicationMode();
    }

    public function preDispatch(): void
    {
    }

    /**
     * Outputs the requested module controller action.
     */
    public function display(array|string|Response|null $actionResult): Response
    {
        if (\is_string($actionResult)) {
            $this->setContent($actionResult);
        } elseif (\is_array($actionResult)) {
            $this->view->assign($actionResult);
        }

        if (empty($this->content) && $this->content !== false) {
            if ($this->template === '') {
                $this->setTemplate($this->applyTemplateAutomatically());
            }

            $content = $this->view->fetchTemplate($this->template);
        } else {
            $content = $this->content === false ? '' : $this->content;
        }

        return new Response($content);
    }

    protected function applyTemplateAutomatically(): string
    {
        return $this->request->getModule()
            . '/' . ucfirst($this->request->getArea())
            . '/' . $this->request->getController()
            . '.' . $this->request->getAction() . '.tpl';
    }

    protected function getApplicationMode(): string
    {
        return $this->applicationMode;
    }

    protected function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Weist dem Template den auszugebenden Inhalt zu.
     *
     * @return $this
     */
    protected function setContent(string|false $data): self
    {
        $this->content = $data;

        return $this;
    }

    /**
     * Setzt das Template der Seite.
     *
     * @return $this
     */
    protected function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Renders the given template with its variables and returns it as a Response object.
     */
    protected function renderTemplate(?string $template, array $templateVariables): Response
    {
        if ($template === null || $template === '') {
            $template = $this->applyTemplateAutomatically();
        }

        $this->view->assign($templateVariables);

        return new Response($this->view->fetchTemplate($template));
    }
}
