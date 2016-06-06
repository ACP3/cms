<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Contact;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Contact\Controller\Widget\Index
 */
class Index extends Core\Controller\WidgetAction
{
    /**
     * @return array
     */
    public function execute()
    {
        return [
            'sidebar_contact' => $this->config->getSettings('contact')
        ];
    }
}
