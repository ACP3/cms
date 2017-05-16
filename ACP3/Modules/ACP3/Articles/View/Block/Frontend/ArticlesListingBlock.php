<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\View\Block\Frontend;


use ACP3\Core\Date;
use ACP3\Core\View\Block\AbstractListingBlock;
use ACP3\Core\View\Block\Context\ListingBlockContext;
use ACP3\Modules\ACP3\Articles\Installer\Schema;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticlesRepository;

class ArticlesListingBlock extends AbstractListingBlock
{
    /**
     * @var Date
     */
    private $date;
    /**
     * @var ArticlesRepository
     */
    private $articleRepository;

    /**
     * Listing constructor.
     * @param ListingBlockContext $context
     * @param Date $date
     * @param ArticlesRepository $articleRepository
     */
    public function __construct(ListingBlockContext $context, Date $date, ArticlesRepository $articleRepository)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->articleRepository = $articleRepository;
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
        return $this->articleRepository->countAll($this->date->getCurrentDateTime());
    }

    /**
     * @inheritdoc
     */
    protected function getResults(int $resultsPerPage): array
    {
        return $this->articleRepository->getAll(
            $this->date->getCurrentDateTime(),
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );
    }

    /**
     * @return array
     */
    public function render(): array
    {
        $resultsPerPage = $this->getResultsPerPage();
        $this->configurePagination($resultsPerPage);

        return [
            'articles' => $this->getResults($resultsPerPage),
            'pagination' => $this->pagination->render()
        ];
    }
}
