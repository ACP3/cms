<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\News;

/**
 * Class Details
 * @package ACP3\Modules\ACP3\News\Controller\Frontend\Index
 */
class Details extends AbstractAction
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\News\Model\NewsRepository
     */
    protected $newsRepository;
    /**
     * @var News\Cache
     */
    protected $newsCache;

    /**
     * Details constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Date                               $date
     * @param \ACP3\Modules\ACP3\News\Model\NewsRepository  $newsRepository
     * @param \ACP3\Modules\ACP3\News\Cache                 $newsCache
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        News\Model\NewsRepository $newsRepository,
        News\Cache $newsCache)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->newsRepository = $newsRepository;
        $this->newsCache = $newsCache;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        if ($this->newsRepository->resultExists($id, $this->date->getCurrentDateTime()) == 1) {
            $news = $this->newsCache->getCache($id);

            $this->breadcrumb->append($this->translator->t('news', 'news'), 'news');

            if ($this->newsSettings['category_in_breadcrumb'] == 1) {
                $this->breadcrumb->append($news['category_title'], 'news/index/index/cat_' . $news['category_id']);
            }
            $this->breadcrumb->append($news['title']);

            $news['target'] = $news['target'] == 2 ? ' target="_blank"' : '';

            return [
                'news' => $news,
                'dateformat' => $this->newsSettings['dateformat'],
                'comments_allowed' => $this->commentsActive === true && $news['comments'] == 1
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }
}
