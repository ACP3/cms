<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\View\Block\Admin;

use ACP3\Core\Modules\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractRepositoryAwareFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\News\Helpers;
use ACP3\Modules\ACP3\News\Installer\Schema;
use ACP3\Modules\ACP3\News\Model\Repository\NewsRepository;

class NewsAdminFormBlock extends AbstractRepositoryAwareFormBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    private $categoriesHelpers;

    /**
     * NewsFormBlock constructor.
     * @param FormBlockContext $context
     * @param NewsRepository $newsRepository
     * @param SettingsInterface $settings
     * @param Modules $modules
     * @param \ACP3\Modules\ACP3\Categories\Helpers $categoriesHelpers
     */
    public function __construct(
        FormBlockContext $context,
        NewsRepository $newsRepository,
        SettingsInterface $settings,
        Modules $modules,
        \ACP3\Modules\ACP3\Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context, $newsRepository);

        $this->settings = $settings;
        $this->modules = $modules;
        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $news = $this->getData();

        $this->title->setPageTitlePrefix($news['title']);

        return [
            'active' => $this->forms->yesNoCheckboxGenerator('active', $news['active']),
            'categories' => $this->categoriesHelpers->categoriesList(
                Schema::MODULE_NAME,
                $news['category_id'],
                true
            ),
            'options' => $this->fetchOptions((int)$news['readmore'], (int)$news['comments']),
            'target' => $this->forms->linkTargetChoicesGenerator('target', $news['target']),
            'form' => array_merge($news, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken(),
            'SEO_URI_PATTERN' => Helpers::URL_KEY_PATTERN,
            'SEO_ROUTE_NAME' => $this->getSeoRouteName((int)$news['id'])
        ];
    }

    /**
     * @param int $readMoreValue
     * @param int $commentsValue
     * @return array
     */
    private function fetchOptions(int $readMoreValue, int $commentsValue): array
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);
        $options = [];
        if ($settings['readmore'] == 1) {
            $readMore = [
                '1' => $this->translator->t('news', 'activate_readmore')
            ];

            $options = $this->forms->checkboxGenerator('readmore', $readMore, $readMoreValue);
        }
        if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
            $comments = [
                '1' => $this->translator->t('system', 'allow_comments')
            ];

            $options = array_merge(
                $options,
                $this->forms->checkboxGenerator('comments', $comments, $commentsValue)
            );
        }

        return $options;
    }

    /**
     * @param int $id
     * @return string
     */
    private function getSeoRouteName(int $id): string
    {
        return !empty($id) ? sprintf(Helpers::URL_KEY_PATTERN, $id) : '';
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [
            'id' => '',
            'title' => '',
            'text' => '',
            'category_id' => 0,
            'readmore' => 0,
            'comments' => 0,
            'uri' => '',
            'link_title' => '',
            'target' => '',
            'active' => 1,
            'start' => '',
            'end' => ''
        ];
    }
}
