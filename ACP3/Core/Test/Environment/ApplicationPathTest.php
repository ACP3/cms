<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\Environment;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;

class ApplicationPathTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ApplicationPath
     */
    private $appPath;

    protected function setUp()
    {
        $this->appPath = new ApplicationPath(ApplicationMode::DEVELOPMENT);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSetDesignPathAbsolute()
    {
        $designPathAbsolute = 'http://example.com/test';

        $this->appPath->setDesignPathAbsolute($designPathAbsolute);
    }

    public function testInvalidSetDesignPathAbsolute()
    {
        $this->expectException(\InvalidArgumentException::class);

        $invalidDesignPathAbsolute = 'http//example.com/test';

        $this->appPath->setDesignPathAbsolute($invalidDesignPathAbsolute);
    }
}
