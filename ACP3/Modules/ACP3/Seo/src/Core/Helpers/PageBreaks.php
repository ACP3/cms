<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Helpers;

use ACP3\Core;
use ACP3\Core\Helpers\TableOfContents;
use ACP3\Core\SEO\MetaStatementsServiceInterface;

class PageBreaks extends Core\Helpers\PageBreaks
{
    /**
     * @var \ACP3\Core\SEO\MetaStatementsServiceInterface
     */
    protected $metaStatements;

    public function __construct(
        Core\Http\RequestInterface $request,
        Core\Router\RouterInterface $router,
        TableOfContents $tableOfContents,
        MetaStatementsServiceInterface $metaStatements
    ) {
        parent::__construct($request, $router, $tableOfContents);

        $this->metaStatements = $metaStatements;
    }

    /**
     * {@inheritdoc}
     */
    public function splitTextIntoPages($text, $baseUrlPath)
    {
        $pages = parent::splitTextIntoPages($text, $baseUrlPath);

        $this->metaStatements->setNextPage($pages['next']);
        $this->metaStatements->setPreviousPage($pages['previous']);

        return $pages;
    }
}
