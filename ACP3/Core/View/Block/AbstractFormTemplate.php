<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Block;


use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\View;

abstract class AbstractFormTemplate extends AbstractTemplate implements FormTemplateInterface
{
    /**
     * @var Forms
     */
    protected $forms;
    /**
     * @var FormToken
     */
    protected $formToken;

    /**
     * @var array
     */
    private $requestData = [];

    /**
     * AbstractFormTemplate constructor.
     * @param View $view
     * @param Forms $forms
     * @param FormToken $formToken
     */
    public function __construct(View $view, Forms $forms, FormToken $formToken)
    {
        parent::__construct($view);

        $this->forms = $forms;
        $this->formToken = $formToken;
    }

    /**
     * @inheritdoc
     */
    public function getData(): array
    {
        return array_merge($this->getDefaultData(), parent::getData());
    }

    /**
     * @inheritdoc
     */
    public function getRequestData(): array
    {
        return $this->requestData;
    }

    /**
     * @inheritdoc
     */
    public function setRequestData(array $requestData)
    {
        $this->requestData = $requestData;

        return $this;
    }
}
