<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\FileResolver;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Environment\AreaMatcher;
use Symfony\Component\HttpFoundation\RequestStack;

class AssetsManifestFileCheckerStrategy implements FileCheckerStrategyInterface
{
    /**
     * @var array<string, mixed>|null
     */
    private ?array $manifest = null;

    public function __construct(
        private readonly ApplicationPath $appPath,
        private readonly RequestStack $requestStack,
        private readonly AreaMatcher $areaMatcher,
        private readonly UserModelInterface $userModel,
    ) {
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

        $entrypoints = $this->getEntrypointNames($resourcePath);

        foreach ($entrypoints as $entrypoint) {
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
        }

        return null;
    }

    /**
     * @return string[]
     */
    private function getEntrypointNames(string $resourcePath): array
    {
        $entrypoints = [];

        $area = $this->areaMatcher->getAreaFromRequest($this->requestStack->getMainRequest());

        if ($area === AreaEnum::AREA_ADMIN) {
            $entrypoints[] = $this->getEntrypointName(
                preg_replace('=/([^/]+)\.(css|js)$=', '/admin-$1.$2', $resourcePath)
            );
        }
        if ($this->userModel->isAuthenticated()) {
            $entrypoints[] = $this->getEntrypointName(
                preg_replace('=/([^/]+)\.(css|js)$=', '/logged-in-$1.$2', $resourcePath)
            );
        }

        $entrypoints[] = $this->getEntrypointName($resourcePath);

        return $entrypoints;
    }

    private function getEntrypointName(string $resourcePath): string
    {
        $fileExtOffset = str_ends_with($resourcePath, '.js') ? -3 : -4;

        return strtolower(str_replace('/', '-', substr($resourcePath, \strlen(ACP3_ROOT_DIR) + 1, $fileExtOffset)));
    }
}
