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
     * @param mixed        $value
     */
    public function assign($name, $value = null): void;

    /**
     * @param string|null $variableName
     *
     * @return mixed
     */
    public function getTemplateVars(?string $variableName = null);

    /**
     * @param string      $template
     * @param string|null $cacheId
     * @param string|null $compileId
     * @param object|null $parent
     *
     * @return string
     */
    public function fetch(string $template, ?string $cacheId = null, ?string $compileId = null, $parent = null): string;

    /**
     * @param string      $template
     * @param string|null $cacheId
     * @param string|null $compileId
     * @param object|null $parent
     */
    public function display(string $template, ?string $cacheId = null, ?string $compileId = null, $parent = null): void;

    /**
     * @param string $template
     *
     * @return bool
     */
    public function templateExists(string $template): bool;
}
