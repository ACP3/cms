<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer;

interface RendererInterface
{
    /**
     * @param mixed[]|string $name
     *
     * @return static
     */
    public function assign(array|string $name, mixed $value = null): self;

    public function getTemplateVars(string $variableName = null): mixed;

    public function fetch(string $template, string $cacheId = null, string $compileId = null, object $parent = null): string;

    public function display(string $template, string $cacheId = null, string $compileId = null, object $parent = null): void;

    public function templateExists(string $template): bool;
}
