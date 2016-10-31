<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Extension;


use ACP3\Modules\ACP3\News\Helpers;
use ACP3\Modules\ACP3\News\Installer\Schema;
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
        switch ($areas) {
            case 'title':
                return 'title';
            case 'content':
                return 'text';
            default:
                return 'title, text';
        }
    }

    /**
     * @return string
     */
    protected function getRouteName()
    {
        return Helpers::URL_KEY_PATTERN;
    }
}
