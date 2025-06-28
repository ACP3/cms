<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\FileResolver;

use ACP3\Core\Environment\ApplicationPath;

class AssetsManifestFileCheckerStrategy implements FileCheckerStrategyInterface
{
    /**
     * @var array<string, mixed>|null
     */
    private ?array $manifest = null;

    public function __construct(private readonly ApplicationPath $appPath)
    {
    }

    public function isAllowed(string $resourcePath): bool
    {
        return str_ends_with($resourcePath, '.js');
    }

    /**
     * @throws \JsonException
     */
    public function findResource(string $resourcePath): ?array
    {
        if ($this->manifest === null) {
            $this->manifest = json_decode(
                file_get_contents($this->appPath->getUploadsDir() . 'assets/assets-manifest.json'),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }

        $entrypoint = $this->getEntrypointName($resourcePath);

        if (\array_key_exists($entrypoint, $this->manifest)) {
            return array_map(
                fn ($path) => $this->appPath->getUploadsDir() . 'assets/' . $path,
                $this->manifest[$entrypoint]['assets']['js']
            );
        }

        return null;
    }

    private function getEntrypointName(string $resourcePath): string
    {
        return strtolower(str_replace('/', '-', substr($resourcePath, \strlen(ACP3_ROOT_DIR) + 1, -3)));
    }
}
