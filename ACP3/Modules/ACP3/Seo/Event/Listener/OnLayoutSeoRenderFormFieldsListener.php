<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Seo\Helper\MetaFormFields;

class OnLayoutSeoRenderFormFieldsListener
{
    /**
     * @var View
     */
    private $view;
    /**
     * @var MetaFormFields
     */
    private $metaFormFields;
    /**
     * @var ACLInterface
     */
    private $acl;

    /**
     * OnLayoutSeoRenderFormFieldsListener constructor.
     * @param ACLInterface $acl
     * @param View $view
     * @param MetaFormFields $metaFormFields
     */
    public function __construct(
        ACLInterface $acl,
        View $view,
        MetaFormFields $metaFormFields
    ) {
        $this->acl = $acl;
        $this->view = $view;
        $this->metaFormFields = $metaFormFields;
    }

    /**
     * @param TemplateEvent $event
     */
    public function renderSeoFormFields(TemplateEvent $event)
    {
        if ($this->acl->hasPermission('admin/seo/index/create')) {
            $parameters = $event->getParameters();

            $formFields = array_merge(
                $this->metaFormFields->formFields(isset($parameters['path']) ? $parameters['path'] : ''),
                ['uri_pattern' => isset($parameters['uri_pattern']) ? $parameters['uri_pattern'] : '']
            );

            $this->view
                ->assign('seo', $formFields)
                ->displayTemplate('Seo/Partials/tab_seo_fields.tpl');
        }
    }
}
