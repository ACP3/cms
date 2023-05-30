<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Core\Router;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Router implements RouterInterface
{
    private const ADMIN_PANEL_PATTERN = '=^(acp|admin)/=';

    public function __construct(protected RequestInterface $request, protected ApplicationPath $appPath, protected SettingsInterface $config)
    {
    }

    public function route(string $path, bool $isAbsolute = false, bool $isSecure = null): string
    {
        if ($path !== '') {
            $path = $this->preparePath($path);

            if ($this->isAdminUri($path) === false) {
                $path .= (!preg_match('/\/$/', $path) ? '/' : '');
            }
        }

        if ($path === '/') {
            $path = '';
        }

        return $this->addUriPrefix($path, $isAbsolute, $isSecure) . $path;
    }

    protected function preparePath(string $path): string
    {
        $path .= (!preg_match('/\/$/', $path) ? '/' : '');
        if ($path === 'acp/') {
            $path = 'acp/acp/index/index/';
        }

        $prefix = 'admin/';
        if (str_starts_with($path, $prefix)) {
            $path = 'acp/' . substr($path, \strlen($prefix));
        }

        return $this->addControllerAndAction($path);
    }

    protected function addControllerAndAction(string $path): string
    {
        $pathArray = preg_split('=/=', $path, -1, PREG_SPLIT_NO_EMPTY);
        $indexes = ($this->isAdminUri($path) === true) ? [2, 3] : [1, 2];

        foreach ($indexes as $index) {
            if (isset($pathArray[$index]) === false) {
                $path .= 'index/';
            }
        }

        if ($this->isHomepage($path) === true) {
            $path = '/';
        }

        return $path;
    }

    protected function isAdminUri(string $path): bool
    {
        return preg_match(self::ADMIN_PANEL_PATTERN, $path) === 1;
    }

    protected function addUriPrefix(string $path, bool $isAbsolute, ?bool $isSecure): string
    {
        $prefix = '';
        if ($isAbsolute === true || $isSecure !== null) {
            $prefix .= $this->getScheme($isSecure);
            $prefix .= $this->request->getHost();
        }

        if ($path === '' || $this->useModRewrite($path)) {
            $prefix .= $this->appPath->getWebRoot();
        } else {
            $prefix .= $this->appPath->getPhpSelf() . '/';
        }

        return $prefix;
    }

    private function getScheme(?bool $isSecure): string
    {
        if ($isSecure === null) {
            return $this->request->getScheme() . '://';
        }
        if ($isSecure === true) {
            return 'https://';
        }

        return 'http://';
    }

    /**
     * Check, whether to use urls with mod_rewrite or not.
     */
    protected function useModRewrite(string $path): bool
    {
        return (bool) $this->getSystemSettings()['mod_rewrite'] === true
            && $this->isAdminUri($path) === false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getSystemSettings(): array
    {
        return $this->config->getSettings(Schema::MODULE_NAME);
    }

    protected function isHomepage(string $path): bool
    {
        return $path === $this->getSystemSettings()['homepage'];
    }
}
