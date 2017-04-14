<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Block\Context;


use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\View;

class TemplateContext
{
    /**
     * @var View
     */
    private $view;
    /**
     * @var Steps
     */
    private $breadcrumb;
    /**
     * @var Title
     */
    private $title;

    /**
     * TemplateContext constructor.
     * @param View $view
     * @param Steps $steps
     * @param Title $title
     */
    public function __construct(View $view, Steps $steps, Title $title)
    {
        $this->view = $view;
        $this->breadcrumb = $steps;
        $this->title = $title;
    }

    /**
     * @return View
     */
    public function getView(): View
    {
        return $this->view;
    }

    /**
     * @return Steps
     */
    public function getBreadcrumb(): Steps
    {
        return $this->breadcrumb;
    }

    /**
     * @return Title
     */
    public function getTitle(): Title
    {
        return $this->title;
    }
}
