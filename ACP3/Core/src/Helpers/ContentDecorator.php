<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core\Helpers\ContentDecorator\ContentDecoratorInterface;

class ContentDecorator
{
    /**
     * @var ContentDecoratorInterface[]
     */
    private array $contentDecorators = [];

    public function registerContentDecorator(ContentDecoratorInterface $contentDecorator, int $priority): void
    {
        $this->contentDecorators[$priority] = $contentDecorator;

        krsort($this->contentDecorators, SORT_NUMERIC);
    }

    /**
     * This method decorates a simple content entity (i.e. without any HTML)
     * and applies the registered formatting rules on it.
     */
    public function decorate(string $content): string
    {
        foreach ($this->contentDecorators as $contentDecorator) {
            $content = $contentDecorator->decorate($content);
        }

        return $content;
    }
}
