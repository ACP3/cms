<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\ContentDecorator;

use ACP3\Core\Helpers\ContentDecorator\ContentDecoratorInterface;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Emoticons\Helpers;
use ACP3\Modules\ACP3\Emoticons\Installer\Schema;

class EmoticonsContentDecorator implements ContentDecoratorInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    private $emoticonsHelpers;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(Modules $modules, Helpers $emoticonsHelpers)
    {
        $this->emoticonsHelpers = $emoticonsHelpers;
        $this->modules = $modules;
    }

    /**
     * {@inheritdoc}
     */
    public function decorate(string $content): string
    {
        if (!$this->modules->isActive(Schema::MODULE_NAME)) {
            return $content;
        }

        return $this->emoticonsHelpers->emoticonsReplace($content);
    }
}
