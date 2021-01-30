<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\EventListener;

use ACP3\Core\Modules;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Emoticons\Helpers;
use ACP3\Modules\ACP3\Emoticons\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnWysiwygTextareaBeforeListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    private $emoticonsHelper;

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
        if (!empty($arguments['id']) && $this->modules->isActive(Schema::MODULE_NAME)) {
            $templateEvent->addContent($this->emoticonsHelper->emoticonsList($arguments['id']));
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'core.wysiwyg.textarea.before' => '__invoke',
        ];
    }
}
