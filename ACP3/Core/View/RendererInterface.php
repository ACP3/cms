<?php

namespace ACP3\Core\View;

/**
 * Interface RendererInterface
 * @package ACP3\Core\View
 */
interface RendererInterface
{
    /**
     * @param      $name
     * @param null $value
     */
    public function assign($name, $value = null);

    /**
     * @param $template
     *
     * @return string
     */
    public function fetch($template);

    /**
     * @param $template
     */
    public function display($template);

    /**
     * @param $template
     *
     * @return bool
     */
    public function templateExists($template);
}
