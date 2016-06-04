<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class DisplayActionTrait
 * @package ACP3\Core\Controller
 */
trait DisplayActionTrait
{
    /**
     * @var string
     */
    private $contentType = 'text/html';
    /**
     * @var string
     */
    private $charset = "UTF-8";
    /**
     * @var string
     */
    private $template = '';

    /**
     * Outputs the requested module controller action
     *
     * @param mixed $actionResult
     * @return Response
     */
    public function display($actionResult)
    {
        if ($actionResult instanceof Response) {
            return $actionResult;
        } elseif (is_string($actionResult)) {
            $this->setContent($actionResult);
        } elseif (is_array($actionResult)) {
            $this->getView()->assign($actionResult);
        }

        // Output content through the controller
        $this->getResponse()->headers->set('Content-type', $this->getContentType());
        $this->getResponse()->setCharset($this->getCharset());

        if (!$this->getContent()) {
            // Set the template automatically
            if ($this->getTemplate() === '') {
                $this->setTemplate($this->applyTemplateAutomatically());
            }

            $this->addCustomTemplateVarsBeforeOutput();

            $this->getResponse()->setContent($this->getView()->fetchTemplate($this->getTemplate()));
        }

        return $this->getResponse();
    }

    /**
     * @return string
     */
    abstract protected function applyTemplateAutomatically();

    /**
     * @return void
     */
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
     * Gibt den Content-Type der anzuzeigenden Seiten zurück
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Weist der aktuell auszugebenden Seite den Content-Type zu
     *
     * @param string $data
     *
     * @return $this
     */
    public function setContentType($data)
    {
        $this->contentType = $data;

        return $this;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     *
     * @return $this
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * Gibt den auszugebenden Seiteninhalt zurück
     *
     * @return string
     */
    public function getContent()
    {
        return $this->getResponse()->getContent();
    }

    /**
     * Weist dem Template den auszugebenden Inhalt zu
     *
     * @param string $data
     *
     * @return $this
     */
    public function setContent($data)
    {
        $this->getResponse()->setContent($data);

        return $this;
    }

    /**
     * Gibt das aktuell zugewiesene Template zurück
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Setzt das Template der Seite
     *
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }
}
