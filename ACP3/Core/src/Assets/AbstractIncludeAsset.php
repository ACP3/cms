<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Application\BootstrapInterface;
use ACP3\Core\Assets;
use ACP3\Core\Environment\ApplicationPath;

abstract class AbstractIncludeAsset
{
    /**
     * @var \ACP3\Core\Assets
     */
    private $assets;
    /**
     * @var \ACP3\Core\Assets\FileResolver
     */
    private $fileResolver;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var array
     */
    private $alreadyIncluded = [];

    public function __construct(
        Assets $assets,
        FileResolver $fileResolver,
        ApplicationPath $appPath
    ) {
        $this->assets = $assets;
        $this->fileResolver = $fileResolver;
        $this->appPath = $appPath;
    }

    /**
     * @param string   $moduleName
     * @param string   $filePath
     * @param string[] $dependencies
     *
     * @return string
     */
    public function add(string $moduleName, string $filePath, array $dependencies = []): string
    {
        if (!empty($dependencies)) {
            $this->assets->enableLibraries($dependencies);
        }

        if (!$this->hasValidParams($moduleName, $filePath)) {
            throw new \InvalidArgumentException(
                'Not all necessary arguments for the function ' . __FUNCTION__ . ' were passed!'
            );
        }

        $key = $moduleName . '/' . $filePath;

        // Do not include the same file multiple times
        if (isset($this->alreadyIncluded[$key])) {
            return '';
        }

        $this->alreadyIncluded[$key] = true;

        return \sprintf(
            $this->getHtmlTag(),
            $this->resolvePath($moduleName, $filePath) . '?v=' . BootstrapInterface::VERSION
        );
    }

    /**
     * @param string $moduleName
     * @param string $filePath
     *
     * @return bool
     */
    private function hasValidParams(string $moduleName, string $filePath): bool
    {
        return \preg_match('=/=', $moduleName) === 0
            && \preg_match('=\./=', $filePath) === 0;
    }

    /**
     * @param string $moduleName
     * @param string $filePath
     *
     * @return string
     */
    private function resolvePath(string $moduleName, string $filePath): string
    {
        $path = $this->fileResolver->getStaticAssetPath(
            $moduleName,
            $this->getResourceDirectory(),
            $filePath . '.' . $this->getFileExtension()
        );

        return $this->appPath->getWebRoot() . \substr($path, \strlen(ACP3_ROOT_DIR . DIRECTORY_SEPARATOR));
    }

    abstract protected function getResourceDirectory(): string;

    abstract protected function getFileExtension(): string;

    abstract protected function getHtmlTag(): string;
}
