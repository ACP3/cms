<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Acp\Event\Listener;


use ACP3\Core\Breadcrumb\Event\GetSiteAndPageTitleBeforeEvent;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;

/**
 * Class OnGetSiteAndPageTitleBeforeListener
 * @package ACP3\Modules\ACP3\Acp\Event\Listener
 */
class OnGetSiteAndPageTitleBeforeListener
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    /**
     * OnGetSiteAndPageTitleBeforeListener constructor.
     *
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\I18n\Translator       $translator
     */
    public function __construct(
        RequestInterface $request,
        Translator $translator
    ) {
        $this->request = $request;
        $this->translator = $translator;
    }

    /**
     * @param \ACP3\Core\Breadcrumb\Event\GetSiteAndPageTitleBeforeEvent $event
     */
    public function execute(GetSiteAndPageTitleBeforeEvent $event)
    {
        if ($this->request->getArea() === AreaEnum::AREA_ADMIN) {
            $this->addPageTitlePostfix($event->getTitle());
        }
    }

    /**
     * @param \ACP3\Core\Breadcrumb\Title $title
     */
    private function addPageTitlePostfix(Title $title)
    {
        if ($this->request->getModule() !== 'acp') {
            if (!empty($title->getPageTitlePostfix())) {
                $title->setPageTitlePostfix(
                    $title->getPageTitlePostfix()
                    . $title->getPageTitleSeparator()
                    . $this->translator->t('system', 'acp')
                );
            } else {
                $title->setPageTitlePostfix($this->translator->t('system', 'acp'));
            }
        }
    }
}
