<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\FileResolver;

class MinifiedAwareFileCheckerStrategy implements FileCheckerStrategyInterface
{
    public function __construct(private readonly StraightFileCheckerStrategy $straightFileCheckerStrategy)
    {
    }

    public function findResource(string $resourcePath): ?string
    {
        $fileExtDot = strrpos($resourcePath, '.');
        $fileExt = substr($resourcePath, $fileExtDot + 1);
        $resourcePath = substr($resourcePath, 0, $fileExtDot) . '.min.' . $fileExt;

        return $this->straightFileCheckerStrategy->findResource($resourcePath);
    }

    public function isAllowed(string $resourcePath): bool
    {
        return (bool) preg_match('/.+(?<!\.min)\.(css|js)$/', $resourcePath);
    }
}
