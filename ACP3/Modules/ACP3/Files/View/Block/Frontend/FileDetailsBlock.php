<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\View\Block\Frontend;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;
use ACP3\Modules\ACP3\Files\Installer\Schema;

class FileDetailsBlock extends AbstractBlock
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
     * FileDetailsBlock constructor.
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
        $file = $this->getData();

        $this->addBreadcrumbSteps($file['category_id']);

        $settings = $this->settings->getSettings(Schema::MODULE_NAME);
        $file['text'] = $this->view->fetchStringAsTemplate($file['text']);

        return [
            'file' => $file,
            'dateformat' => $settings['dateformat'],
            'comments_allowed' => $settings['comments'] == 1 && $file['comments'] == 1,
        ];
    }

    private function addBreadcrumbSteps(int $categoryId)
    {
        $this->breadcrumb->append($this->translator->t('files', 'files'), 'files');

        foreach ($this->categoriesRepository->fetchNodeWithParents($categoryId) as $category) {
            $this->breadcrumb->append($category['title'], 'files/index/files/cat_' . $category['id']);
        }

        $this->breadcrumb->append($this->getData()['title']);
        $this->title->setPageTitle($this->getData()['title']);
    }
}
