<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\ContentDecorator;

use ACP3\Core\Helpers\ContentDecorator\ContentDecoratorInterface;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Emoticons\Installer\Schema;
use ACP3\Modules\ACP3\Emoticons\Services\EmoticonServiceInterface;

class EmoticonsContentDecorator implements ContentDecoratorInterface
{
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var EmoticonServiceInterface
     */
    private $emoticonService;

    public function __construct(Modules $modules, EmoticonServiceInterface $emoticonService)
    {
        $this->modules = $modules;
        $this->emoticonService = $emoticonService;
    }

    /**
     * {@inheritdoc}
     */
    public function decorate(string $content): string
    {
        if (!$this->modules->isInstalled(Schema::MODULE_NAME)) {
            return $content;
        }

        return strtr($content, $this->emoticonService->getEmoticonList());
    }
}
