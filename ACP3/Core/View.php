<?php

namespace ACP3\Core;

use ACP3\Core\View\Renderer\RendererInterface;

/**
 * Klasse für die Ausgabe der Seite
 * @package ACP3\Core
 */
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
     * @param mixed       $cacheId
     * @param mixed       $compileId
     * @param object|null $parent
     */
    public function displayTemplate($template, $cacheId = null, $compileId = null, $parent = null)
    {
        $this->renderer->display('asset:' . $template, $cacheId, $compileId, $parent);
    }

    /**
     * Fetches a template and returns its contents
     *
     * @param string      $template
     * @param mixed       $cacheId
     * @param mixed       $compileId
     * @param object|null $parent
     *
     * @return string
     */
    public function fetchTemplate($template, $cacheId = null, $compileId = null, $parent = null)
    {
        return $this->renderer->fetch('asset:' . $template, $cacheId, $compileId, $parent);
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
        return $this->renderer->templateExists($template);
    }

    /**
     * Assigns a new template variable
     *
     * @param string|array $name
     * @param mixed        $value
     *
     * @return boolean
     */
    public function assign($name, $value = null)
    {
        return $this->renderer->assign($name, $value);
    }
}
