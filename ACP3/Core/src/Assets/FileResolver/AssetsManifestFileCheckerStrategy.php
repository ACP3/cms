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
        return str_ends_with($resourcePath, '.js') || str_ends_with($resourcePath, '.css');
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
            if (\is_array($this->manifest[$entrypoint])) {
                $resolvedPaths = [
                    ...$this->manifest[$entrypoint]['assets']['js'],
                    ...($this->manifest[$entrypoint]['assets']['css'] ?? []),
                ];

                return array_map(
                    fn ($path) => $this->appPath->getUploadsDir() . 'assets/' . $path,
                    $resolvedPaths
                );
            }

            return $this->manifest[$entrypoint];
        }

        return null;
    }

    private function getEntrypointName(string $resourcePath): string
    {
        if (str_ends_with($resourcePath, '.js')) {
            return strtolower(str_replace('/', '-', substr($resourcePath, \strlen(ACP3_ROOT_DIR) + 1, -3)));
        }

        return strtolower(str_replace('/', '-', substr($resourcePath, \strlen(ACP3_ROOT_DIR) + 1, -4)));
    }
}
