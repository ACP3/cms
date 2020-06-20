<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

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
     * @param Response|string|array $actionResult
     */
    public function display($actionResult): Response
    {
        if ($actionResult instanceof Response) {
            foreach ($this->getResponse()->headers->getCookies() as $cookie) {
                $actionResult->headers->setCookie($cookie);
            }

            return $actionResult;
        }

        if (\is_string($actionResult)) {
            $this->setContent($actionResult);
        } elseif (\is_array($actionResult)) {
            $this->getView()->assign($actionResult);
        }

        if (empty($this->getContent()) && $this->getContent() !== false) {
            if ($this->getTemplate() === '') {
                $this->setTemplate($this->applyTemplateAutomatically());
            }

            $this->addCustomTemplateVarsBeforeOutput();

            $content = $this->getView()->fetchTemplate($this->getTemplate());
        } else {
            $content = $this->getContent() === false ? '' : $this->getContent();
        }

        $this->getResponse()->setContent($content);

        return $this->getResponse();
    }

    /**
     * @return string
     */
    abstract protected function applyTemplateAutomatically();

    abstract protected function addCustomTemplateVarsBeforeOutput();

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    abstract protected function getResponse();

    /**
     * @return \ACP3\Core\View
     */
    abstract protected function getView();

    /**
     * Gibt den Content-Type der anzuzeigenden Seiten zurück.
     */
    public function getContentType(): string
    {
        return $this->getResponse()->headers->get('Content-type');
    }

    /**
     * Weist der aktuell auszugebenden Seite den Content-Type zu.
     *
     * @return $this
     */
    public function setContentType(string $data): self
    {
        $this->getResponse()->headers->set('Content-type', $data);

        return $this;
    }

    /**
     * Gibt den auszugebenden Seiteninhalt zurück.
     *
     * @return string|false
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Weist dem Template den auszugebenden Inhalt zu.
     *
     * @param string|false $data
     *
     * @return $this
     */
    public function setContent($data): self
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
}
