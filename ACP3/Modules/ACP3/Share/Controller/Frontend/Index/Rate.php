<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Frontend\Index;


use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Modules\ACP3\Comments\Controller\Frontend\Index\AbstractFrontendAction;
use ACP3\Modules\ACP3\Share\Model\Repository\ShareRatingsRepository;
use ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository;
use ACP3\Modules\ACP3\Share\Model\ShareRatingModel;

class Rate extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository
     */
    private $shareRepository;
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\Repository\ShareRatingsRepository
     */
    private $shareRatingsRepository;
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\ShareRatingModel
     */
    private $shareRatingModel;

    /**
     * Rate constructor.
     * @param \ACP3\Core\Controller\Context\FrontendContext                    $context
     * @param \ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository        $shareRepository
     * @param \ACP3\Modules\ACP3\Share\Model\Repository\ShareRatingsRepository $shareRatingsRepository
     * @param \ACP3\Modules\ACP3\Share\Model\ShareRatingModel                  $shareRatingModel
     */
    public function __construct(
        FrontendContext $context,
        ShareRepository $shareRepository,
        ShareRatingsRepository $shareRatingsRepository,
        ShareRatingModel $shareRatingModel)
    {
        parent::__construct($context);

        $this->shareRepository = $shareRepository;
        $this->shareRatingsRepository = $shareRatingsRepository;
        $this->shareRatingModel = $shareRatingModel;
    }

    /**
     * @param int $id
     * @param int $stars
     * @return array
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id, int $stars): array
    {
        if (!($stars >= 1 && $stars <= 5)) {
            throw new ResultNotExistsException();
        }
        if ($this->shareRepository->resultExistsById($id) === false) {
            throw new ResultNotExistsException();
        }

        $this->shareRatingModel->save([
            'share_id' => $id,
            'stars' => $stars
        ]);

        return [
            'rating' => $this->shareRatingsRepository->getRatingStatistics($id)
        ];
    }
}
