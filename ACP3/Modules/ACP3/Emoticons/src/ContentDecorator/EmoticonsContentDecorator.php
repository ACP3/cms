<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\ContentDecorator;

use ACP3\Core\Helpers\ContentDecorator\ContentDecoratorInterface;
use ACP3\Modules\ACP3\Emoticons\Helpers;

class EmoticonsContentDecorator implements ContentDecoratorInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    private $emoticonsHelpers;

    public function __construct(Helpers $emoticonsHelpers)
    {
        $this->emoticonsHelpers = $emoticonsHelpers;
    }

    /**
     * {@inheritdoc}
     */
    public function decorate(string $content): string
    {
        return $this->emoticonsHelpers->emoticonsReplace($content);
    }
}
