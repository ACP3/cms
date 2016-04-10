<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Acp\Event\Listener;


use ACP3\Core\Breadcrumb\Event\BreadcrumbStepsBuildCacheEvent;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;

class OnBreadcrumbStepsBuildCacheListener
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    /**
     * OnBreadcrumbStepsBuildCacheListener constructor.
     *
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\Breadcrumb\Title      $title
     * @param \ACP3\Core\I18n\Translator       $translator
     */
    public function __construct(
        RequestInterface $request,
        Title $title,
        Translator $translator)
    {
        $this->request = $request;
        $this->title = $title;
        $this->translator = $translator;
    }

    /**
     * @param \ACP3\Core\Breadcrumb\Event\BreadcrumbStepsBuildCacheEvent $event
     */
    public function onBuildCache(BreadcrumbStepsBuildCacheEvent $event)
    {
        if ($this->request->getArea() === AreaEnum::AREA_ADMIN) {
            $this->addPageTitlePostfix();
        }
    }

    private function addPageTitlePostfix()
    {
        if ($this->request->getModule() !== 'acp') {
            if (!empty($this->title->getPageTitlePostfix())) {
                $this->title->setPageTitlePostfix(
                    $this->title->getPageTitlePostfix()
                    . $this->title->getPageTitleSeparator()
                    . $this->translator->t('system', 'acp')
                );
            } else {
                $this->title->setPageTitlePostfix($this->translator->t('system', 'acp'));
            }
        }
    }
}
