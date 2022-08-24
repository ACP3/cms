<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Picture;

use ACP3\Core\Environment\ApplicationPath;
use FastImageSize\FastImageSize;
use PHPUnit\Framework\TestCase;

class PictureTest extends TestCase
{
    private static string $cacheDir = '/tmp/acp3-picture-cache';

    public function testProcessShouldNotResizePictureIfNotNecessary(): void
    {
        $applicationPath = $this->createMock(ApplicationPath::class);

        $picture = new Picture(new FastImageSize(), $applicationPath);

        $input = (new Input())
            ->setCacheDir(self::$cacheDir)
            ->setFile(\dirname(__DIR__, 2) . '/fixtures/150.png');

        $output = $picture->process($input);

        self::assertSame(150, $output->getWidth());
        self::assertSame(150, $output->getHeight());
        self::assertSame($output->getSrcFile(), $output->getFile());

        self::assertDirectoryDoesNotExist($input->getCacheDir());
    }

    public function testProcessShouldResizePictureIfNecessary(): void
    {
        $applicationPath = $this->createMock(ApplicationPath::class);

        $picture = new Picture(new FastImageSize(), $applicationPath);

        $input = (new Input())
            ->setEnableCache(true)
            ->setCacheDir(self::$cacheDir)
            ->setFile(\dirname(__DIR__, 2) . '/fixtures/150.png')
            ->setMaxWidth(100)
            ->setMaxHeight(100);

        $output = $picture->process($input);

        self::assertSame(100, $output->getWidth());
        self::assertSame(100, $output->getHeight());
        self::assertNotSame($output->getSrcFile(), $output->getFile());

        self::assertDirectoryExists($input->getCacheDir());
        self::assertFileExists($output->getFile());

        unlink($output->getDestFile());
        rmdir(self::$cacheDir);
    }
}
