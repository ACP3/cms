<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Environment;

use ACP3\Core\Controller\AreaEnum;
use Symfony\Component\HttpFoundation\Request;

class AreaMatcher
{
    private const ADMIN_PANEL_PATTERN = '/acp/';
    private const WIDGET_PATTERN = '/widget/';
    private const INSTALLER_PATTERN = '/installation/';

    public function getAreaFromRequest(Request $symfonyRequest): AreaEnum
    {
        $requestUri = $symfonyRequest->getRequestUri();

        if (str_starts_with($requestUri, self::ADMIN_PANEL_PATTERN)) {
            return AreaEnum::AREA_ADMIN;
        }
        if (str_starts_with($requestUri, self::WIDGET_PATTERN)) {
            return AreaEnum::AREA_WIDGET;
        }
        if (str_starts_with($requestUri, self::INSTALLER_PATTERN)) {
            return AreaEnum::AREA_INSTALL;
        }

        return AreaEnum::AREA_FRONTEND;
    }
}
