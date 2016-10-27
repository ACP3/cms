<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;


use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;

class OnLayoutSeoRenderFormFieldsListener
{
    /**
     * @var View
     */
    private $view;

    /**
     * OnLayoutSeoFormFieldsListener constructor.
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * @param TemplateEvent $event
     */
    public function renderSeoFormFields(TemplateEvent $event)
    {
        $parameters = $event->getParameters();
        $this->view->assign('seo', isset($parameters['SEO_FORM_FIELDS']) ? $parameters['SEO_FORM_FIELDS'] : []);
        $this->view->displayTemplate('Seo/Partials/tab_seo_fields.tpl');
    }
}
