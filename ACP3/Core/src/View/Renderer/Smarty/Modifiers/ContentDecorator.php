<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

class ContentDecorator extends AbstractModifier
{
    public function __construct(private \ACP3\Core\Helpers\ContentDecorator $contentDecorator)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(string $value): string
    {
        return $this->contentDecorator->decorate($value);
    }
}
