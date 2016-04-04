<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Breadcrumb;

use ACP3\Core\Config;

/**
 * Class Title
 * @package ACP3\Modules\ACP3\Seo\Core\Breadcrumb
 */
class Title extends \ACP3\Core\Breadcrumb\Title
{
    /**
     * Title constructor.
     *
     * @param \ACP3\Core\Config $config
     */
    public function __construct(Config $config)
    {
        $this->siteTitle = $config->getSettings('seo')['title'];
    }
}
