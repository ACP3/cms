<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Share\Helpers\SocialServices;
use ACP3\Modules\ACP3\Share\Installer\Schema;
use Heise\Shariff\Backend;

class BackendFactory
{
    public function __construct(private ApplicationPath $applicationPath, private SettingsInterface $settings, private RequestInterface $request, private SocialServices $socialServices)
    {
    }

    public function create(): Backend
    {
        $this->checkCacheDir();

        return new Backend($this->getOptions());
    }

    private function checkCacheDir(): void
    {
        if (is_dir($this->getCacheDir())) {
            return;
        }

        if (!mkdir($concurrentDirectory = $this->getCacheDir()) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }

    private function getCacheDir(): string
    {
        return $this->applicationPath->getCacheDir() . 'shariff/';
    }

    private function getOptions(): array
    {
        return array_merge(
            [
                'domains' => [$this->request->getHttpHost()],
                'cache' => [
                    'ttl' => 60,
                    'cacheDir' => $this->getCacheDir(),
                    'adapter' => 'Filesystem',
                ],
                'services' => $this->socialServices->getActiveBackendServices(),
            ],
            $this->getFacebookCredentials()
        );
    }

    private function getFacebookCredentials(): array
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        if (!empty($settings['fb_app_id']) && !empty($settings['fb_secret'])) {
            return [
                'Facebook' => [
                    'app_id' => $settings['fb_app_id'],
                    'secret' => $settings['fb_secret'],
                ],
            ];
        }

        return [];
    }
}
