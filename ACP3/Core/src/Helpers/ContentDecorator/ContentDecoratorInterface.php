<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\ContentDecorator;

interface ContentDecoratorInterface
{
    /**
     * Decorates the given simple content entity (i.e. without any HTML) and applies some formatting on it.
     */
    public function decorate(string $content): string;
}
