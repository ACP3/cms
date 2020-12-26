<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Errors\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Controller\Context\FrontendContext;
use Symfony\Component\HttpFoundation\Response;

class AccessForbidden extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $breadcrumb;

    public function __construct(
        FrontendContext $context,
        Steps $breadcrumb
    ) {
        parent::__construct($context);

        $this->breadcrumb = $breadcrumb;
    }

    public function execute(): Response
    {
        $this->breadcrumb->append(
            $this->translator->t('errors', 'frontend_index_access_forbidden'),
            $this->request->getQuery()
        );

        return new Response(
            $this->view->fetchTemplate('Errors/Frontend/index.access_forbidden.tpl'),
            Response::HTTP_FORBIDDEN
        );
    }
}
