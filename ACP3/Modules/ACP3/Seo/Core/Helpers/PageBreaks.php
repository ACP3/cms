<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Helpers;

use ACP3\Core;
use ACP3\Core\Helpers\TableOfContents;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

/**
 * Class PageBreaks
 * @package ACP3\Modules\ACP3\Seo\Core\Helpers
 */
class PageBreaks extends \ACP3\Core\Helpers\PageBreaks
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatements;

    /**
     * PageBreaks constructor.
     *
     * @param \ACP3\Core\Http\RequestInterface             $request
     * @param \ACP3\Core\RouterInterface                   $router
     * @param \ACP3\Core\Helpers\TableOfContents           $tableOfContents
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements $metaStatements
     */
    public function __construct(
        Core\Http\RequestInterface $request,
        Core\RouterInterface $router,
        TableOfContents $tableOfContents,
        MetaStatements $metaStatements
    ) {
        parent::__construct($request, $router, $tableOfContents);

        $this->metaStatements = $metaStatements;
    }

    /**
     * @inheritdoc
     */
    public function splitTextIntoPages($text, $baseUrlPath)
    {
        $pages = parent::splitTextIntoPages($text, $baseUrlPath);

        $this->metaStatements->setNextPage($pages['next']);
        $this->metaStatements->setPreviousPage($pages['previous']);

        return $pages;
    }

}
