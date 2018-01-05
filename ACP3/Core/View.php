<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\View\Renderer\RendererInterface;

class View
{
    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * Gets the renderer
     *
     * @return RendererInterface
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * View constructor.
     *
     * @param \ACP3\Core\View\Renderer\RendererInterface $renderer
     * @param array                                      $params
     */
    public function __construct(RendererInterface $renderer, array $params = [])
    {
        $this->renderer = $renderer;
        $this->renderer->configure($params);
    }

    /**
     * Fetches a template and outputs its contents
     *
     * @param string      $template
     */
    public function displayTemplate($template)
    {
        $this->renderer->display('asset:' . $template);
    }

    /**
     * Fetches a template and returns its contents
     *
     * @param string      $template
     *
     * @return string
     */
    public function fetchTemplate($template)
    {
        return $this->renderer->fetch('asset:' . $template);
    }

    /**
     * @param string $template
     *
     * @return string
     */
    public function fetchStringAsTemplate($template)
    {
        try {
            return $this->renderer->fetch('string:' . $template);
        } catch (\Exception $e) {
            return $template; // fail silently when invalid statements have been given in the string template
        }
    }

    /**
     * Checks, whether a template exists or not
     *
     * @param string $template
     *
     * @return boolean
     */
    public function templateExists($template)
    {
        return $this->renderer->templateExists('asset:' . $template);
    }

    /**
     * Assigns a new template variable
     *
     * @param string|array $name
     * @param mixed        $value
     *
     * @return $this
     */
    public function assign($name, $value = null)
    {
        $this->renderer->assign($name, $value);

        return $this;
    }
}
