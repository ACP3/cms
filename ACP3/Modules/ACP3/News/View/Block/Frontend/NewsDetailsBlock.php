<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\View\Block\Frontend;


use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\News\Installer\Schema;

class NewsDetailsBlock extends AbstractBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * NewsDetailsBlock constructor.
     * @param BlockContext $context
     * @param SettingsInterface $settings
     */
    public function __construct(BlockContext $context, SettingsInterface $settings)
    {
        parent::__construct($context);

        $this->settings = $settings;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $news = $this->getData();
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $this->breadcrumb->append($this->translator->t('news', 'news'), 'news');
        if ($settings['category_in_breadcrumb'] == 1) {
            $this->breadcrumb->append($news['category_title'], 'news/index/index/cat_' . $news['category_id']);
        }
        $this->breadcrumb->append($news['title']);

        $news['text'] = $this->view->fetchStringAsTemplate($news['text']);
        $news['target'] = $news['target'] == 2 ? ' target="_blank"' : '';

        return [
            'news' => $news,
            'dateformat' => $settings['dateformat'],
            'comments_allowed' => $settings['comments'] == 1 && $news['comments'] == 1
        ];

    }
}
