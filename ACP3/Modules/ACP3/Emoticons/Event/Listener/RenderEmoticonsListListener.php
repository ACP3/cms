<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Event\Listener;

use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Emoticons\Helpers;

class RenderEmoticonsListListener
{
    /**
     * @var Helpers
     */
    private $emoticonsHelpers;

    /**
     * RenderEmoticonsListListener constructor.
     *
     * @param Helpers $emoticonsHelpers
     */
    public function __construct(Helpers $emoticonsHelpers)
    {
        $this->emoticonsHelpers = $emoticonsHelpers;
    }

    /**
     * @param TemplateEvent $event
     */
    public function __invoke(TemplateEvent $event)
    {
        $parameters = $event->getParameters();
        $formFieldId = !empty($parameters['form_field_id']) ? $parameters['form_field_id'] : '';

        echo $this->emoticonsHelpers->emoticonsList($formFieldId);
    }
}
