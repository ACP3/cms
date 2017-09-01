<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\View\Block;

use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Core\View\Block\FormBlockInterface;

abstract class AbstractFormBlockTest extends AbstractBlockTest
{
    /**
     * @var FormBlockInterface
     */
    protected $block;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();
    }

    /**
     * @inheritdoc
     */
    protected function getContextMockFQCN(): string
    {
        return FormBlockContext::class;
    }

    /**
     * @inheritdoc
     */
    protected function getContextMockMethods(): array
    {
        return ['getView', 'getBreadcrumb', 'getTitle', 'getTranslator', 'getForms', 'getFormToken'];
    }
}
