<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Block;


use ACP3\Core\View;

abstract class AbstractTemplate implements TemplateInterface
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
     * @var array
     */
    private $data = [];

    /**
     * AbstractTemplate constructor.
     * @param Context\TemplateContext $context
     */
    public function __construct(View\Block\Context\TemplateContext $context)
    {
        $this->view = $context->getView();
        $this->breadcrumb = $context->getBreadcrumb();
        $this->title = $context->getTitle();
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
}
