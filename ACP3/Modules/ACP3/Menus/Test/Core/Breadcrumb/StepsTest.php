<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Test\Core\Breadcrumb;


use ACP3\Modules\ACP3\Menus\Core\Breadcrumb\Steps;
use ACP3\Modules\ACP3\Menus\Model\MenuItemRepository;

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

        $this->menuItemRepositoryMock = $this->getMockBuilder(MenuItemRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
