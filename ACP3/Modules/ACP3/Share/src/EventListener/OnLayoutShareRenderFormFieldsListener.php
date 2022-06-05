<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Share\Helpers\ShareFormFields;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnLayoutShareRenderFormFieldsListener implements EventSubscriberInterface
{
    public function __construct(private readonly ACL $acl, private readonly View $view, private readonly ShareFormFields $shareFormFields)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(TemplateEvent $event): void
    {
        if ($this->acl->hasPermission('admin/share/index/create')) {
            $parameters = $event->getParameters();

            $formFields = [...$this->shareFormFields->formFields($parameters['path'] ?? ''), ...['uri_pattern' => $parameters['uri_pattern'] ?? '']];

            $this->view->assign('share', $formFields);

            $event->addContent($this->view->fetchTemplate('Share/Partials/tab_share_fields.tpl'));
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
