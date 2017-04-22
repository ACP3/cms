<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Block\Context;


use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\I18n\Translator;
use ACP3\Core\View;

class BlockContext
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
     * @var Translator
     */
    private $translator;

    /**
     * TemplateContext constructor.
     * @param View $view
     * @param Steps $steps
     * @param Title $title
     * @param Translator $translator
     */
    public function __construct(View $view, Steps $steps, Title $title, Translator $translator)
    {
        $this->view = $view;
        $this->breadcrumb = $steps;
        $this->title = $title;
        $this->translator = $translator;
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

    /**
     * @return Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
