<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\View\Block\Widget;


use ACP3\Core\Date;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticlesRepository;

class ArticlesLatestBlock extends AbstractBlock
{
    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticlesRepository
     */
    private $articlesRepository;

    public function __construct(
        BlockContext $context,
        Date $date,
        ArticlesRepository $articlesRepository)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->articlesRepository = $articlesRepository;
    }

    /**
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function render()
    {
        return [
            'sidebar_articles' => $this->articlesRepository->getAll($this->date->getCurrentDateTime(), 5),
        ];
    }
}
