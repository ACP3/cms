<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\FileResolver;

use ACP3\Core\Environment\ApplicationPath;

class StraightFileCheckerStrategy implements FileCheckerStrategyInterface
{
    public function __construct(private readonly ApplicationPath $applicationPath)
    {
    }

    public function findResource(string $resourcePath): ?array
    {
        $productionResourcePath = str_replace(ACP3_ROOT_DIR, $this->applicationPath->getUploadsDir() . 'assets', $resourcePath);
        if (is_file($productionResourcePath)) {
            $hash = hash('crc32b', (string) file_get_contents($productionResourcePath));

            return [$productionResourcePath . '?' . $hash];
        }

        return null;
    }

    public function isAllowed(string $resourcePath): bool
    {
        return true;
    }
}
