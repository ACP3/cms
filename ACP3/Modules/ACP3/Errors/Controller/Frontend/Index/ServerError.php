<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Errors\Controller\Frontend\Index;

use ACP3\Core;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Errors\Controller\Frontend\Index
 */
class ServerError extends Core\Controller\AbstractFrontendAction
{
    public function execute()
    {
        $this->breadcrumb->append($this->translator->t('errors', 'frontend_index_server_error'));

        $this->response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
