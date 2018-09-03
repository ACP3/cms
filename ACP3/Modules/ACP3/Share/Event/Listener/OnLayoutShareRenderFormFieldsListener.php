<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Share\Helpers\ShareFormFields;

class OnLayoutShareRenderFormFieldsListener
{
    /**
     * @var View
     */
    private $view;
    /**
     * @var ACL
     */
    private $acl;
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\ShareFormFields
     */
    private $shareFormFields;

    /**
     * OnLayoutShareRenderFormFieldsListener constructor.
     *
     * @param \ACP3\Core\ACL                                   $acl
     * @param \ACP3\Core\View                                  $view
     * @param \ACP3\Modules\ACP3\Share\Helpers\ShareFormFields $shareFormFields
     */
    public function __construct(
        ACL $acl,
        View $view,
        ShareFormFields $shareFormFields
    ) {
        $this->acl = $acl;
        $this->view = $view;
        $this->shareFormFields = $shareFormFields;
    }

    /**
     * @param TemplateEvent $event
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(TemplateEvent $event)
    {
        if ($this->acl->hasPermission('admin/share/index/create')) {
            $parameters = $event->getParameters();

            $formFields = \array_merge(
                $this->shareFormFields->formFields($parameters['path'] ?? ''),
                ['uri_pattern' => $parameters['uri_pattern'] ?? '']
            );

            $this->view
                ->assign('share', $formFields)
                ->displayTemplate('Share/Partials/tab_share_fields.tpl');
        }
    }
}
