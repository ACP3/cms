<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Errors\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Controller\Context\WidgetContext;
use Symfony\Component\HttpFoundation\Response;

class NotFound extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $breadcrumb;

    public function __construct(
        WidgetContext $context,
        Steps $breadcrumb
    ) {
        parent::__construct($context);

        $this->breadcrumb = $breadcrumb;
    }

    public function __invoke(): Response
    {
        $this->breadcrumb->append(
            $this->translator->t('errors', 'frontend_index_not_found'),
            $this->request->getQuery()
        );

        return new Response(
            $this->view->fetchTemplate('Errors/Frontend/index.not_found.tpl'),
            Response::HTTP_NOT_FOUND
        );
    }
}
