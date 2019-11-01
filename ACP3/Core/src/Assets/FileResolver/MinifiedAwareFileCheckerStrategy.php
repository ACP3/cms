<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\FileResolver;

class MinifiedAwareFileCheckerStrategy implements FileCheckerStrategyInterface
{
    public function findResource(string $resourcePath): ?string
    {
        $fileExtDot = \strrpos($resourcePath, '.');
        $fileExt = \substr($resourcePath, $fileExtDot + 1);
        $resourcePath = \substr($resourcePath, 0, $fileExtDot) . '.min.' . $fileExt;

        if (\is_file($resourcePath)) {
            return $resourcePath;
        }

        return null;
    }

    public function isAllowed(string $resourcePath): bool
    {
        return (bool) \preg_match('/.+(?<!\.min)\.(css|js)$/', $resourcePath);
    }
}
