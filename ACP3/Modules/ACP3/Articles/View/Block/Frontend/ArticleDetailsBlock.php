<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\View\Block\Frontend;

use ACP3\Core\Helpers\PageBreaks;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;

class ArticleDetailsBlock extends AbstractBlock
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var PageBreaks
     */
    private $pageBreaksHelper;

    /**
     * ArticleDetailsBlock constructor.
     * @param BlockContext $context
     * @param RequestInterface $request
     * @param PageBreaks $pageBreaksHelper
     */
    public function __construct(BlockContext $context, RequestInterface $request, PageBreaks $pageBreaksHelper)
    {
        parent::__construct($context);

        $this->request = $request;
        $this->pageBreaksHelper = $pageBreaksHelper;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $article = $this->getData();

        $this->breadcrumb->append($article['title']);

        return [
            'page' => array_merge(
                $article,
                $this->pageBreaksHelper->splitTextIntoPages(
                    $this->view->fetchStringAsTemplate($article['text']),
                    $this->request->getUriWithoutPages()
                )
            )
        ];
    }
}
