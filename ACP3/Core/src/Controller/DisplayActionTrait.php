<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core\View;
use Symfony\Component\HttpFoundation\Response;

trait DisplayActionTrait
{
    /**
     * @var string
     */
    private $template = '';
    /**
     * @var string|false
     */
    private $content = '';

    /**
     * Outputs the requested module controller action.
     *
     * @param string|array|null $actionResult
     */
    public function display($actionResult): Response
    {
        if (\is_string($actionResult)) {
            $this->setContent($actionResult);
        } elseif (\is_array($actionResult)) {
            $this->getView()->assign($actionResult);
        }

        if (empty($this->getContent()) && $this->getContent() !== false) {
            if ($this->getTemplate() === '') {
                $this->setTemplate($this->applyTemplateAutomatically());
            }

            $content = $this->getView()->fetchTemplate($this->getTemplate());
        } else {
            $content = $this->getContent() === false ? '' : $this->getContent();
        }

        return new Response($content);
    }

    abstract protected function applyTemplateAutomatically(): string;

    abstract protected function getView(): View;

    /**
     * Gibt den auszugebenden Seiteninhalt zurück.
     */
    public function getContent(): string|false
    {
        return $this->content;
    }

    /**
     * Weist dem Template den auszugebenden Inhalt zu.
     *
     * @return $this
     */
    public function setContent(string|false $data): self
    {
        $this->content = $data;

        return $this;
    }

    /**
     * Gibt das aktuell zugewiesene Template zurück.
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * Setzt das Template der Seite.
     *
     * @return $this
     */
    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Renders the given template with its variables and returns it as a Response object.
     */
    public function renderTemplate(?string $template, array $templateVariables): Response
    {
        if ($template === null || $template === '') {
            $template = $this->applyTemplateAutomatically();
        }

        $this->getView()->assign($templateVariables);

        return new Response($this->getView()->fetchTemplate($template));
    }
}
