<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filessearch\Extension;

use ACP3\Modules\ACP3\Files\Helpers;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use ACP3\Modules\ACP3\Search\Extension\AbstractSearchAvailabilityExtension;

class SearchAvailabilityExtension extends AbstractSearchAvailabilityExtension
{
    /**
     * @return string
     */
    public function getModuleName()
    {
        return Schema::MODULE_NAME;
    }

    /**
     * @param string $areas
     *
     * @return string
     */
    protected function mapSearchAreasToFields($areas)
    {
        return match ($areas) {
            'title' => 'title, file',
            'content' => 'text',
            default => 'title, file, text',
        };
    }

    /**
     * @return string
     */
    protected function getRouteName()
    {
        return Helpers::URL_KEY_PATTERN;
    }
}
