<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Contact;
use ACP3\Modules\ACP3\System\Installer\Schema;

/**
 * Class Imprint
 * @package ACP3\Modules\ACP3\Contact\Controller\Frontend\Index
 */
class Imprint extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @return array
     */
    public function execute()
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        return [
            'imprint' => $this->config->getSettings(Contact\Installer\Schema::MODULE_NAME),
            'powered_by' => $this->translator->t(
                'contact',
                'powered_by',
                [
                    '%ACP3%' => '<a href="http://www.acp3-cms.net" target="_blank">ACP3</a>'
                ]
            )
        ];
    }
}
