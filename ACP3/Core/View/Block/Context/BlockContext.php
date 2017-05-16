<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Block\Context;


use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\I18n\TranslatorInterface;
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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * TemplateContext constructor.
     * @param View $view
     * @param Steps $steps
     * @param Title $title
     * @param TranslatorInterface $translator
     */
    public function __construct(View $view, Steps $steps, Title $title, TranslatorInterface $translator)
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
     * @return TranslatorInterface
     */
    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }
}
