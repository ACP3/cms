<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\FileResolver;

interface FileCheckerStrategyInterface
{
    public function isAllowed(string $resourcePath): bool;

    public function findResource(string $resourcePath): ?string;
}
