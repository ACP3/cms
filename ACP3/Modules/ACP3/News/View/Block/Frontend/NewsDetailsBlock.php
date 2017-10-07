<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\View\Block\Frontend;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;
use ACP3\Modules\ACP3\News\Installer\Schema;

class NewsDetailsBlock extends AbstractBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var CategoriesRepository
     */
    private $categoriesRepository;

    /**
     * NewsDetailsBlock constructor.
     * @param BlockContext $context
     * @param SettingsInterface $settings
     * @param CategoriesRepository $categoriesRepository
     */
    public function __construct(
        BlockContext $context,
        SettingsInterface $settings,
        CategoriesRepository $categoriesRepository
    ) {
        parent::__construct($context);

        $this->settings = $settings;
        $this->categoriesRepository = $categoriesRepository;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $news = $this->getData();
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $this->addBreadcrumbSteps($news['category_id'], $settings['category_in_breadcrumb']);

        $news['text'] = $this->view->fetchStringAsTemplate($news['text']);
        $news['target'] = $news['target'] == 2 ? ' target="_blank"' : '';

        return [
            'news' => $news,
            'dateformat' => $settings['dateformat'],
            'comments_allowed' => $settings['comments'] == 1 && $news['comments'] == 1
        ];
    }

    private function addBreadcrumbSteps(int $categoryId, bool $showCategoriesInBreadcrumb)
    {
        $news = $this->getData();

        $this->breadcrumb->append($this->translator->t('news', 'news'), 'news');
        if ($showCategoriesInBreadcrumb === true) {
            foreach ($this->categoriesRepository->fetchNodeWithParents($categoryId) as $category) {
                $this->breadcrumb->append($category['title'], 'news/index/index/cat_' . $category['id']);
            }
        }
        $this->breadcrumb->append($news['title']);
    }
}
