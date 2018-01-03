<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Test\View\Block\Admin;

use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticlesRepository;
use ACP3\Modules\ACP3\Articles\View\Block\Admin\ArticleAdminFormBlock;

class ArticleAdminFormBlockTest extends AbstractFormBlockTest
{
    /**
     * @var ArticlesRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    protected function setUpMockObjects()
    {
        $this->repository = $this->getMockBuilder(ArticlesRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        parent::setUpMockObjects();
    }

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new ArticleAdminFormBlock($this->context, $this->repository);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'active',
            'form',
            'form_token',
            'SEO_URI_PATTERN',
            'SEO_ROUTE_NAME'
        ];
    }
}
