<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Errors\Controller\Frontend\Index;

use ACP3\Core;

/**
 * Class AccessForbidden
 * @package ACP3\Modules\ACP3\Errors\Controller\Frontend\Index
 */
class AccessForbidden extends Core\Controller\FrontendAction
{
    public function execute()
    {
        $this->breadcrumb->append($this->translator->t('errors', 'frontend_index_access_forbidden'));

        $this->response->setStatusCode(403);
    }
}
