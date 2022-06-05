<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\ContentDecorator;

use ACP3\Core\Helpers\StringFormatter;

class Nl2pContentDecorator implements ContentDecoratorInterface
{
    public function __construct(private readonly StringFormatter $stringFormatter)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function decorate(string $content): string
    {
        return $this->stringFormatter->nl2p($content);
    }
}
