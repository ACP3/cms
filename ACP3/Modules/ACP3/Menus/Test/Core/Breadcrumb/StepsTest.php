<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Test\Core\Breadcrumb;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Modules\ACP3\Menus\Core\Breadcrumb\Steps;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemsRepository;

class StepsTest extends \ACP3\Core\Test\Breadcrumb\StepsTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $menuItemRepositoryMock;

    protected function setUp()
    {
        $this->initializeMockObjects();

        $this->steps = new Steps(
            $this->containerMock,
            $this->translatorMock,
            $this->requestMock,
            $this->routerMock,
            $this->eventDispatcherMock,
            $this->menuItemRepositoryMock
        );
    }

    protected function initializeMockObjects()
    {
        parent::initializeMockObjects();

        $this->menuItemRepositoryMock = $this->getMockBuilder(MenuItemsRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function setUpMenuItemRepositoryExpectations(array $dbSteps = [])
    {
        $this->menuItemRepositoryMock->expects($this->once())
            ->method('getMenuItemsByUri')
            ->withAnyParameters()
            ->willReturn($dbSteps);
    }

    public function testGetBreadcrumbWithSingleDbStep()
    {
        $this->setUpMenuItemRepositoryExpectations([
            [
                'title' => 'News',
                'uri' => 'news',
                'left_id' => 1,
                'right_id' => 4
            ]
        ]);
        $this->setUpRequestMockExpectations(
            AreaEnum::AREA_FRONTEND,
            'news',
            'index',
            'index'
        );
        $this->setUpRouterMockExpectations();
        $this->setUpTranslatorMockExpectations(0);

        $expected = [
            [
                'title' => 'News',
                'uri' => '/news/',
                'last' => true
            ]
        ];
        $this->assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testGetBreadcrumbWithMultipleDbSteps()
    {
        $this->setUpMenuItemRepositoryExpectations([
            [
                'title' => 'News',
                'uri' => 'news',
                'left_id' => 1,
                'right_id' => 4
            ],
            [
                'title' => 'Newsletter',
                'uri' => 'newsletter',
                'left_id' => 2,
                'right_id' => 3
            ],
        ]);
        $this->setUpRequestMockExpectations(
            AreaEnum::AREA_FRONTEND,
            'newsletter',
            'index',
            'index'
        );
        $this->setUpRouterMockExpectations();
        $this->setUpTranslatorMockExpectations(0);

        $expected = [
            [
                'title' => 'News',
                'uri' => '/news/',
            ],
            [
                'title' => 'Newsletter',
                'uri' => '/newsletter/',
                'last' => true
            ],
        ];
        $this->assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testGetBreadcrumbWithMultipleDbStepsAndDefaultSteps()
    {
        $this->setUpMenuItemRepositoryExpectations([
            [
                'title' => 'News',
                'uri' => 'news',
                'left_id' => 1,
                'right_id' => 4
            ],
            [
                'title' => 'Newsletter',
                'uri' => 'newsletter',
                'left_id' => 2,
                'right_id' => 3
            ],
        ]);
        $this->setUpRequestMockExpectations(
            AreaEnum::AREA_FRONTEND,
            'newsletter',
            'index',
            'archive'
        );
        $this->setUpRouterMockExpectations();
        $this->setUpTranslatorMockExpectations(0);

        $expected = [
            [
                'title' => 'News',
                'uri' => '/news/',
            ],
            [
                'title' => 'Newsletter',
                'uri' => '/newsletter/',
            ],
            [
                'title' => '{NEWSLETTER_FRONTEND_INDEX_ARCHIVE}',
                'uri' => '/newsletter/index/archive/',
                'last' => true
            ]
        ];
        $this->assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testGetBreadcrumbWithMultipleDbStepsAndCustomSteps()
    {
        $this->setUpMenuItemRepositoryExpectations([
            [
                'title' => 'News',
                'uri' => 'news',
                'left_id' => 1,
                'right_id' => 4
            ]
        ]);
        $this->setUpRequestMockExpectations(
            AreaEnum::AREA_FRONTEND,
            'news',
            'index',
            'details',
            'id_1'
        );
        $this->setUpRouterMockExpectations();
        $this->setUpTranslatorMockExpectations(0);

        $this->steps->append('News', 'news');
        $this->steps->append('Category', 'news/index/index/cat_1');
        $this->steps->append('News-Title');

        $expected = [
            [
                'title' => 'News',
                'uri' => '/news/',
            ],
            [
                'title' => 'Category',
                'uri' => '/news/index/index/cat_1/',
            ],
            [
                'title' => 'News-Title',
                'uri' => '',
                'last' => true
            ],
        ];
        $this->assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testGetBreadcrumbLastDbStepTitleShouldTakePrecedence()
    {
        $this->setUpMenuItemRepositoryExpectations([
            [
                'title' => 'FooBar',
                'uri' => 'articles/index/details/id_1/',
                'left_id' => 1,
                'right_id' => 2
            ]
        ]);
        $this->setUpRequestMockExpectations(
            AreaEnum::AREA_FRONTEND,
            'articles',
            'index',
            'details',
            'id_1'
        );
        $this->setUpRouterMockExpectations();
        $this->setUpTranslatorMockExpectations(0);

        $this->steps->append('Lorem Ipsum Dolor', 'articles/index/details/id_1');

        $expected = [
            [
                'title' => 'FooBar',
                'uri' => '/articles/index/details/id_1/',
                'last' => true
            ],
        ];
        $this->assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testGetBreadcrumbLastDbStepTitleShouldTakePrecedenceWithEmptyUri()
    {
        $this->setUpMenuItemRepositoryExpectations([
            [
                'title' => 'FooBar',
                'uri' => 'articles/index/details/id_1/',
                'left_id' => 1,
                'right_id' => 2
            ]
        ]);
        $this->setUpRequestMockExpectations(
            AreaEnum::AREA_FRONTEND,
            'articles',
            'index',
            'details',
            'id_1'
        );
        $this->setUpRouterMockExpectations();
        $this->setUpTranslatorMockExpectations(0);

        $this->steps->append('Lorem Ipsum Dolor');

        $expected = [
            [
                'title' => 'FooBar',
                'uri' => '/articles/index/details/id_1/',
                'last' => true
            ],
        ];
        $this->assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testGetBreadcrumbForFrontendControllerIndex()
    {
        $this->setUpMenuItemRepositoryExpectations();

        parent::testGetBreadcrumbForFrontendControllerIndex();
    }

    public function testGetBreadcrumbForFrontendController()
    {
        $this->setUpMenuItemRepositoryExpectations();

        parent::testGetBreadcrumbForFrontendController();
    }

    public function testGetBreadcrumbForFrontendWithExistingSteps()
    {
        $this->setUpMenuItemRepositoryExpectations();

        parent::testGetBreadcrumbForFrontendWithExistingSteps();
    }

    public function testAddMultipleSameSteps()
    {
        $this->setUpMenuItemRepositoryExpectations();

        parent::testReplaceAncestor();
    }

    public function testReplaceAncestor()
    {
        $this->setUpMenuItemRepositoryExpectations();

        parent::testReplaceAncestor();
    }
}
