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
    private $renderer;

    /**
     * Gets the renderer.
     */
    public function getRenderer(): RendererInterface
    {
        return $this->renderer;
    }

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Fetches a template and outputs its contents.
     */
    public function displayTemplate(string $template): void
    {
        $this->renderer->display('asset:' . $template);
    }

    /**
     * Fetches a template and returns its contents.
     */
    public function fetchTemplate(string $template): string
    {
        return $this->renderer->fetch('asset:' . $template);
    }

    public function fetchStringAsTemplate(string $template): string
    {
        try {
            return $this->renderer->fetch('string:' . $template);
        } catch (\Exception $e) {
            return $template; // fail silently when invalid statements have been given in the string template
        }
    }

    /**
     * Checks, whether a template exists or not.
     */
    public function templateExists(string $template): bool
    {
        return $this->renderer->templateExists('asset:' . $template);
    }

    /**
     * Assigns a new template variable.
     *
     * @param string|array $name
     * @param mixed        $value
     *
     * @return $this
     */
    public function assign($name, $value = null): self
    {
        $this->renderer->assign($name, $value);

        return $this;
    }

    public function setLayout(string $layout): void
    {
        $this->assign('LAYOUT', $layout);
    }
}
