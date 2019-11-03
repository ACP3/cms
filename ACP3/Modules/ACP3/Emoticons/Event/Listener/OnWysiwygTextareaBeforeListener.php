<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Event\Listener;

use ACP3\Core\Modules;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Emoticons\Helpers;

class OnWysiwygTextareaBeforeListener
{
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    protected $emoticonsHelper;

    /**
     * OnAfterFormListener constructor.
     */
    public function __construct(
        Modules $modules,
        Helpers $emoticonsHelper
    ) {
        $this->modules = $modules;
        $this->emoticonsHelper = $emoticonsHelper;
    }

    public function __invoke(TemplateEvent $templateEvent)
    {
        $arguments = $templateEvent->getParameters();
        if ($this->modules->isActive('emoticons') && !empty($arguments['id'])) {
            echo $this->emoticonsHelper->emoticonsList($arguments['id']);
        }
    }
}
