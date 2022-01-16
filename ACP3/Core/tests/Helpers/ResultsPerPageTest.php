<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core\Settings\SettingsInterface;

class ResultsPerPageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ResultsPerPage
     */
    private $resultsPerPage;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|SettingsInterface
     */
    private $settingsMock;

    protected function setup(): void
    {
        $this->setUpMockObjects();

        $this->resultsPerPage = new ResultsPerPage($this->settingsMock);
    }

    private function setUpMockObjects()
    {
        $this->settingsMock = $this->createMock(SettingsInterface::class);
    }

    public function testGetResultsPerPageWithoutFallback()
    {
        $this->settingsMock
            ->expects(self::once())
            ->method('getSettings')
            ->with('news')
            ->willReturn(['entries' => 10]);

        $expected = 10;
        self::assertEquals($expected, $this->resultsPerPage->getResultsPerPage('news'));
    }

    public function testGetResultsPerPageWithFallback()
    {
        $this->settingsMock
            ->expects(self::exactly(2))
            ->method('getSettings')
            ->withConsecutive(['news'], ['system'])
            ->willReturnOnConsecutiveCalls([], ['entries' => 20]);

        $expected = 20;
        self::assertEquals($expected, $this->resultsPerPage->getResultsPerPage('news'));
    }
}
