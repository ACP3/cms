<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Block;

use ACP3\Core\View;

abstract class AbstractBlock implements BlockInterface
{
    /**
     * @var View
     */
    protected $view;
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    protected $breadcrumb;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    protected $title;
    /**
     * @var \ACP3\Core\I18n\TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    private $data = [];

    /**
     * AbstractTemplate constructor.
     * @param Context\BlockContext $context
     */
    public function __construct(View\Block\Context\BlockContext $context)
    {
        $this->view = $context->getView();
        $this->breadcrumb = $context->getBreadcrumb();
        $this->title = $context->getTitle();
        $this->translator = $context->getTranslator();
    }

    /**
     * @inheritdoc
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function setTemplate(string $templateName)
    {
        $this->view->setTemplate($templateName);

        return $this;
    }
}
