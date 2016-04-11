<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Errors\Controller\Frontend\Index;

use ACP3\Core;

/**
 * Class NotFound
 * @package ACP3\Modules\ACP3\Errors\Controller\Frontend\Index
 */
class NotFound extends Core\Controller\FrontendAction
{
    public function execute()
    {
        $this->breadcrumb->append($this->translator->t('errors', 'frontend_index_not_found'));

        $this->response->setStatusCode(404);
    }
}
