<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

class ContentDecorator extends AbstractModifier
{
    /**
     * @var \ACP3\Core\Helpers\ContentDecorator
     */
    private $contentDecorator;

    public function __construct(\ACP3\Core\Helpers\ContentDecorator $contentDecorator)
    {
        $this->contentDecorator = $contentDecorator;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($value): string
    {
        return $this->contentDecorator->decorate($value);
    }
}
