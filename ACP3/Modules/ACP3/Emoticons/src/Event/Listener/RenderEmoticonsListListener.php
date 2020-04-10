<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Event\Listener;

use ACP3\Core\Modules;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Emoticons\Helpers;
use ACP3\Modules\ACP3\Emoticons\Installer\Schema;

class RenderEmoticonsListListener
{
    /**
     * @var Helpers
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

    public function __invoke(TemplateEvent $event)
    {
        if (!$this->modules->isActive(Schema::MODULE_NAME)) {
            return;
        }

        $parameters = $event->getParameters();
        $formFieldId = !empty($parameters['form_field_id']) ? $parameters['form_field_id'] : '';

        echo $this->emoticonsHelpers->emoticonsList($formFieldId);
    }
}
