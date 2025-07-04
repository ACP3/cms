<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Assets\Renderer\Strategies\RendererStrategyInterface;

abstract class AbstractIncludeAsset
{
    public function __construct(
        private readonly Libraries $libraries,
        private readonly FileResolver $fileResolver,
        private readonly RendererStrategyInterface $rendererStrategy,
    ) {
    }

    /**
     * @param string[] $dependencies
     */
    public function add(string $moduleName, string $filePath, array $dependencies = []): void
    {
        if (!empty($dependencies)) {
            $this->libraries->enableLibraries($dependencies);
        }

        if (!$this->hasValidParams($moduleName, $filePath)) {
            throw new \InvalidArgumentException('Not all necessary arguments for the function ' . __FUNCTION__ . ' were passed!');
        }

        $this->rendererStrategy->addFiles($this->resolvePath($moduleName, $filePath));
    }

    private function hasValidParams(string $moduleName, string $filePath): bool
    {
        return preg_match('=/=', $moduleName) === 0
            && preg_match('=\./=', $filePath) === 0;
    }

    /**
     * @return string[]
     */
    protected function resolvePath(string $moduleName, string $filePath): array
    {
        $filePath .= '.' . $this->getFileExtension();

        $paths = $this->fileResolver->getStaticAssetPath(
            $moduleName,
            $this->getResourceDirectory(),
            $filePath
        );

        if (empty($paths)) {
            throw new \RuntimeException(\sprintf('Could not find the requested file %s of module %s!', $filePath, $moduleName));
        }

        return $paths;
    }

    abstract protected function getResourceDirectory(): string;

    abstract protected function getFileExtension(): string;
}
