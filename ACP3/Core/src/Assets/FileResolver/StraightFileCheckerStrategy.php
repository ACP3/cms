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

    public function findResource(string $resourcePath): ?string
    {
        // Test, if the production assets for this file have been created.
        // If yes, use this path, otherwise try to fall back to the path within the theme/module itself
        $productionResourcePath = str_replace(ACP3_ROOT_DIR, $this->applicationPath->getUploadsDir() . 'assets', $resourcePath);
        if (is_file($productionResourcePath)) {
            return $productionResourcePath;
        }

        if (is_file($resourcePath)) {
            return $resourcePath;
        }

        return null;
    }

    public function isAllowed(string $resourcePath): bool
    {
        return true;
    }
}
