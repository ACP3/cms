<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\View\Block\Frontend;

use ACP3\Core\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractListingBlock;
use ACP3\Core\View\Block\Context\ListingBlockContext;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;

class FilesListingBlock extends AbstractListingBlock
{
    /**
     * @var Date
     */
    private $date;
    /**
     * @var FilesRepository
     */
    private $filesRepository;
    /**
     * @var CategoriesRepository
     */
    private $categoryRepository;
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * FilesListingBlock constructor.
     * @param ListingBlockContext $context
     * @param Date $date
     * @param SettingsInterface $settings
     * @param FilesRepository $filesRepository
     * @param CategoriesRepository $categoryRepository
     */
    public function __construct(
        ListingBlockContext $context,
        Date $date,
        SettingsInterface $settings,
        FilesRepository $filesRepository,
        CategoriesRepository $categoryRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->filesRepository = $filesRepository;
        $this->categoryRepository = $categoryRepository;
        $this->settings = $settings;
    }

    /**
     * @inheritdoc
     */
    protected function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }

    /**
     * @inheritdoc
     */
    protected function getTotalResults(): int
    {
        $data = $this->getData();

        return $this->filesRepository->countAll($this->date->getCurrentDateTime(), $data['category_id']);
    }

    /**
     * @inheritdoc
     */
    protected function getResults(int $resultsPerPage): array
    {
        $data = $this->getData();

        return $this->filesRepository->getAllByCategoryId(
            $data['category_id'],
            $this->date->getCurrentDateTime(),
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        $this->addBreadcrumbSteps($data['category_id']);

        $settings = $this->settings->getSettings($this->getModuleName());

        $resultsPerPage = $this->getResultsPerPage();
        $this->configurePagination($resultsPerPage);

        return [
            'categories' => $this->categoryRepository->getAllDirectSiblings($data['category_id']),
            'dateformat' => $settings['dateformat'],
            'files' => $this->getResults($resultsPerPage),
            'pagination' => $this->pagination->render()
        ];
    }

    private function addBreadcrumbSteps(int $categoryId)
    {
        $this->breadcrumb
            ->append($this->translator->t('files', 'files'), 'files');

        foreach ($this->categoryRepository->fetchNodeWithParents($categoryId) as $category) {
            $this->breadcrumb->append(
                $category['title'],
                'files/index/files/cat_' . $category['id']
            );
        }
    }
}
