<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller;


use Symfony\Component\HttpFoundation\Response;

/**
 * Class DisplayControllerActionTrait
 * @package ACP3\Core\Controller
 */
trait DisplayActionTrait
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;

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
     * @param mixed $controllerActionResult
     */
    public function display($controllerActionResult)
    {
        if ($controllerActionResult instanceof Response) {
            $controllerActionResult->send();
            return;
        } else {
            if (is_array($controllerActionResult)) {
                $this->view->assign($controllerActionResult);
            } else {
                if (is_string($controllerActionResult)) {
                    echo $controllerActionResult;
                    return;
                }
            }
        }

        // Output content through the controller
        $this->response->headers->set('Content-Type', $this->getContentType());
        $this->response->setCharset($this->getCharset());

        if (!$this->getContent()) {
            // Set the template automatically
            if ($this->getTemplate() === '') {
                $this->setTemplate($this->applyTemplateAutomatically());
            }

            $this->addCustomTemplateVarsBeforeOutput();

            $this->response->setContent($this->view->fetchTemplate($this->getTemplate()));
        }

        $this->response->send();
    }

    /**
     * @return string
     */
    abstract protected function applyTemplateAutomatically();

    abstract protected function addCustomTemplateVarsBeforeOutput();

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
        return $this->response->getContent();
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
        $this->response->setContent($data);

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