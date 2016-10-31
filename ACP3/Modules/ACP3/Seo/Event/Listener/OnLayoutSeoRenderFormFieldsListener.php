<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;


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
     * OnLayoutSeoFormFieldsListener constructor.
     * @param View $view
     * @param MetaFormFields $metaFormFields
     */
    public function __construct(
        View $view,
        MetaFormFields $metaFormFields
    ) {
        $this->view = $view;
        $this->metaFormFields = $metaFormFields;
    }

    /**
     * @param TemplateEvent $event
     */
    public function renderSeoFormFields(TemplateEvent $event)
    {
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
