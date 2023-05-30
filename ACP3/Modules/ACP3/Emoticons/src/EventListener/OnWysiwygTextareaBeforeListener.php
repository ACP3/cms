<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\EventListener;

use ACP3\Core\Modules;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Emoticons\Installer\Schema;
use ACP3\Modules\ACP3\Emoticons\Services\EmoticonServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnWysiwygTextareaBeforeListener implements EventSubscriberInterface
{
    public function __construct(private readonly Modules $modules, private readonly View $view, private readonly EmoticonServiceInterface $emoticonService)
    {
    }

    public function __invoke(TemplateEvent $templateEvent): void
    {
        $arguments = $templateEvent->getParameters();
        if (!empty($arguments['id']) && $this->modules->isInstalled(Schema::MODULE_NAME)) {
            $templateEvent->addContent($this->renderEmotionList($arguments['id']));
        }
    }

    private function renderEmotionList(string $formFieldId): string
    {
        $this->view->assign('emoticons_field_id', empty($formFieldId) ? 'message' : $formFieldId);
        $this->view->assign('emoticons', $this->emoticonService->getEmoticonList());

        return $this->view->fetchTemplate('Emoticons/Partials/emoticons.tpl');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'core.wysiwyg.textarea.before' => '__invoke',
        ];
    }
}
