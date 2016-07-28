<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\System\Installer\Schema;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Articles\Controller\Frontend\Index
 */
class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository
     */
    protected $articleRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext       $context
     * @param \ACP3\Core\Date                                     $date
     * @param \ACP3\Core\Pagination                               $pagination
     * @param \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository $articleRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Articles\Model\Repository\ArticleRepository $articleRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->articleRepository = $articleRepository;
    }

    public function execute()
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        $time = $this->date->getCurrentDateTime();
        $articles = $this->articleRepository->getAll(
            $time,
            $this->pagination->getResultsStartOffset(),
            $this->user->getEntriesPerPage()
        );
        $this->pagination->setTotalResults($this->articleRepository->countAll($time));

        return [
            'articles' => $articles,
            'pagination' => $this->pagination->render()
        ];
    }
}
