<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Acp\Event\Listener;

use ACP3\Core\Breadcrumb\Event\GetSiteAndPageTitleBeforeEvent;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;

class AddAdminPanelTitlePostfixListener
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        RequestInterface $request,
        Translator $translator
    ) {
        $this->request = $request;
        $this->translator = $translator;
    }

    public function __invoke(GetSiteAndPageTitleBeforeEvent $event)
    {
        if ($this->request->getArea() === AreaEnum::AREA_ADMIN) {
            $this->addPageTitlePostfix($event->getTitle());
        }
    }

    private function addPageTitlePostfix(Title $title)
    {
        if ($this->request->getModule() !== 'acp') {
            if (!empty($title->getPageTitlePostfix())) {
                $title->setPageTitlePostfix(
                    $title->getPageTitlePostfix()
                    . $title->getPageTitleSeparator()
                    . $this->translator->t('acp', 'acp')
                );
            } else {
                $title->setPageTitlePostfix($this->translator->t('acp', 'acp'));
            }
        }
    }
}
