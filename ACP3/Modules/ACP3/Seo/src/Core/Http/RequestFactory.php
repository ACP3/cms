<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Http;

use ACP3\Core\Environment\AreaMatcher;
use ACP3\Core\Http\Request as BaseRequest;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Seo\Installer\Schema;
use ACP3\Modules\ACP3\Seo\Repository\SeoRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestFactory
{
    public function __construct(
        private readonly Modules $modules,
        private readonly RequestStack $requestStack,
        private readonly SeoRepository $seoRepository,
        private readonly AreaMatcher $areaMatcher,
    ) {
    }

    public function create(): RequestInterface
    {
        if ($this->modules->isInstalled(Schema::MODULE_NAME)) {
            return new Request($this->requestStack, $this->areaMatcher, $this->seoRepository);
        }

        return new BaseRequest($this->requestStack, $this->areaMatcher);
    }
}
