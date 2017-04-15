<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Block\Context;


use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;

class FormBlockContext extends BlockContext
{
    /**
     * @var Forms
     */
    private $forms;
    /**
     * @var FormToken
     */
    private $formToken;

    /**
     * FormTemplateContext constructor.
     * @param BlockContext $context
     * @param Forms $forms
     * @param FormToken $formToken
     */
    public function __construct(BlockContext $context, Forms $forms, FormToken $formToken)
    {
        parent::__construct($context->getView(), $context->getBreadcrumb(), $context->getTitle());

        $this->forms = $forms;
        $this->formToken = $formToken;
    }

    /**
     * @return Forms
     */
    public function getForms(): Forms
    {
        return $this->forms;
    }

    /**
     * @return FormToken
     */
    public function getFormToken(): FormToken
    {
        return $this->formToken;
    }
}
