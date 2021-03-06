<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer;

interface RendererInterface
{
    /**
     * @param string|array $name
     * @param null         $value
     *
     * @return $this
     */
    public function assign($name, $value = null);

    /**
     * @param string|null $variableName
     *
     * @return mixed
     */
    public function getTemplateVars($variableName = null);

    /**
     * @param string      $template
     * @param mixed       $cacheId
     * @param mixed       $compileId
     * @param object|null $parent
     *
     * @return string
     */
    public function fetch($template, $cacheId = null, $compileId = null, $parent = null);

    /**
     * @param string      $template
     * @param mixed       $cacheId
     * @param mixed       $compileId
     * @param object|null $parent
     */
    public function display($template, $cacheId = null, $compileId = null, $parent = null);

    /**
     * @param string $template
     *
     * @return bool
     */
    public function templateExists($template);
}
