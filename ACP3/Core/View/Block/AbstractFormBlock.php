<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Block;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\View\Block\Context\FormBlockContext;

abstract class AbstractFormBlock extends AbstractBlock implements FormBlockInterface
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
     * @param Context\FormBlockContext $context
     */
    public function __construct(FormBlockContext $context)
    {
        parent::__construct($context);

        $this->forms = $context->getForms();
        $this->formToken = $context->getFormToken();
    }

    /**
     * @inheritdoc
     */
    public function getData(): array
    {
        return \array_merge($this->getDefaultData(), parent::getData());
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
