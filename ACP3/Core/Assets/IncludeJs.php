<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core;

class IncludeJs
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

    /**
     * @param \ACP3\Core\Assets                      $assets
     * @param \ACP3\Core\Assets\FileResolver         $fileResolver
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(
        Core\Assets $assets,
        Core\Assets\FileResolver $fileResolver,
        Core\Environment\ApplicationPath $appPath
    ) {
        $this->assets = $assets;
        $this->fileResolver = $fileResolver;
        $this->appPath = $appPath;
    }

    /**
     * @param string   $module
     * @param string   $filePath
     * @param string[] $dependencies
     *
     * @return string
     */
    public function add(string $module, string $filePath, array $dependencies = []): string
    {
        if (!empty($dependencies)) {
            $this->assets->enableLibraries($dependencies);
        }

        if ($this->hasValidParams($module, $filePath)) {
            $key = $module . '/' . $filePath;

            // Do not include the same file multiple times
            if (isset($this->alreadyIncluded[$key]) === false) {
                $this->alreadyIncluded[$key] = true;

                return \sprintf(
                    '<script defer src="%s"></script>',
                    $this->resolvePath($module, $filePath) . '?v=' . Core\Application\BootstrapInterface::VERSION
                );
            }

            return '';
        }

        if (empty($dependencies)) {
            throw new \InvalidArgumentException(
                'Not all necessary arguments for the function ' . __FUNCTION__ . ' were passed!'
            );
        }

        return '';
    }

    /**
     * @param string $module
     * @param string $filePath
     *
     * @return bool
     */
    private function hasValidParams(string $module, string $filePath): bool
    {
        return isset($module, $filePath) === true
            && (bool) \preg_match('=/=', $module) === false
            && (bool) \preg_match('=\./=', $filePath) === false;
    }

    /**
     * @param string $module
     * @param string $filePath
     *
     * @return string
     */
    protected function resolvePath(string $module, string $filePath): string
    {
        $module = \ucfirst($module);

        $path = $this->fileResolver->getStaticAssetPath(
            $module . '/Resources/',
            $module . '/',
            'Assets/js',
            $filePath . '.js'
        );

        if (\strpos($path, '/ACP3/Modules/') !== false) {
            $path = $this->appPath->getWebRoot() . \substr($path, \strpos($path, '/ACP3/Modules/') + 1);
        } else {
            $path = $this->appPath->getWebRoot() . \substr($path, \strlen(ACP3_ROOT_DIR));
        }

        return $path;
    }
}
