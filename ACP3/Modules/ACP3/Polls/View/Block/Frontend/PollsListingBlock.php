<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\View\Block\Frontend;

use ACP3\Core\Date;
use ACP3\Core\View\Block\AbstractListingBlock;
use ACP3\Core\View\Block\Context\ListingBlockContext;
use ACP3\Modules\ACP3\Polls\Installer\Schema;
use ACP3\Modules\ACP3\Polls\Model\Repository\PollsRepository;

class PollsListingBlock extends AbstractListingBlock
{
    /**
     * @var Date
     */
    private $date;
    /**
     * @var PollsRepository
     */
    private $pollRepository;

    /**
     * PollsListingBlock constructor.
     *
     * @param ListingBlockContext $context
     * @param Date                $date
     * @param PollsRepository     $pollRepository
     */
    public function __construct(ListingBlockContext $context, Date $date, PollsRepository $pollRepository)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pollRepository = $pollRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $resultsPerPage = $this->getResultsPerPage();
        $this->configurePagination($resultsPerPage);

        return [
            'polls' => $this->getResults($resultsPerPage),
            'pagination' => $this->pagination->render(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTotalResults(): int
    {
        return $this->pollRepository->countAll($this->date->getCurrentDateTime());
    }

    /**
     * {@inheritdoc}
     */
    protected function getResults(int $resultsPerPage): array
    {
        $data = $this->getData();

        $polls = $this->pollRepository->getAll(
            $data['user_id'],
            $data['ip_address'],
            $this->date->getCurrentDateTime(),
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );
        $cPolls = \count($polls);

        for ($i = 0; $i < $cPolls; ++$i) {
            if ($polls[$i]['has_voted'] == 1 ||
                $polls[$i]['start'] !== $polls[$i]['end'] && $this->date->timestamp($polls[$i]['end']) <= $this->date->timestamp()
            ) {
                $polls[$i]['link'] = 'result';
            } else {
                $polls[$i]['link'] = 'vote';
            }
        }

        return $polls;
    }
}
