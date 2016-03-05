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
     * @param string      $template
     * @param string|null $cacheId
     * @param string|null $compileId
     * @param object|null $parent
     *
     * @return string
     */
    public function fetch($template, $cacheId = null, $compileId = null, $parent = null);

    /**
     * @param string      $template
     * @param string|null $cacheId
     * @param string|null $compileId
     * @param object|null $parent
     *
     * @return void
     */
    public function display($template, $cacheId = null, $compileId = null, $parent = null);

    /**
     * @param string $template
     *
     * @return bool
     */
    public function templateExists($template);
}
