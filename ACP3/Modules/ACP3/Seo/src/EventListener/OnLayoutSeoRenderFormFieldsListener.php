<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Seo\Helper\MetaFormFields;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnLayoutSeoRenderFormFieldsListener implements EventSubscriberInterface
{
    public function __construct(private readonly ACL $acl, private readonly View $view, private readonly MetaFormFields $metaFormFields)
    {
    }

    public function __invoke(TemplateEvent $event): void
    {
        if ($this->acl->hasPermission('admin/seo/index/create')) {
            $parameters = $event->getParameters();

            $formFields = [...$this->metaFormFields->formFields($parameters['path'] ?? ''), ...['uri_pattern' => $parameters['uri_pattern'] ?? '']];

            $this->view->assign('seo', $formFields);

            $event->addContent($this->view->fetchTemplate('Seo/Partials/tab_seo_fields.tpl'));
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'core.layout.form_extension' => '__invoke',
        ];
    }
}
