<?php

namespace ACP3\Core\View\Renderer;

/**
 * Interface RendererInterface
 * @package ACP3\Core\View
 */
interface RendererInterface
{
    /**
     * @param string|array $name
     * @param null         $value
     */
    public function assign($name, $value = null);

    /**
     * @param string $template
     *
     * @return string
     */
    public function fetch($template);

    /**
     * @param string $template
     */
    public function display($template);

    /**
     * @param string $template
     *
     * @return bool
     */
    public function templateExists($template);
}
