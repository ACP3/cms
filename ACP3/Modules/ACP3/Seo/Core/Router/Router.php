<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Router;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;

class Router extends \ACP3\Core\Router\Router
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Core\Router\Aliases
     */
    protected $aliases;

    /**
     * @param \ACP3\Modules\ACP3\Seo\Core\Router\Aliases $aliases
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Settings\SettingsInterface $config
     * @param string $environment
     */
    public function __construct(
        Aliases $aliases,
        RequestInterface $request,
        ApplicationPath $appPath,
        SettingsInterface $config,
        $environment
    ) {
        parent::__construct($request, $appPath, $config, $environment);

        $this->aliases = $aliases;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function preparePath($path)
    {
        $path = parent::preparePath($path);

        if ($this->isAdminUri($path) === false) {
            $path = $this->aliases->getUriAlias($path);
        }

        return $path;
    }
}
