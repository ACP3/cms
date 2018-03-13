<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Frontend\Index;

use ACP3\Core\Controller\Context\FrontendContext;
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
     *
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
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id): array
    {
        $stars = $this->request->getPost()->get('stars');
        $ipAddress = $this->request->getSymfonyRequest()->getClientIp();

        if ($this->canSaveRating($id, $stars, $ipAddress) === true) {
            $this->shareRatingModel->save([
                'share_id' => $id,
                'stars' => $stars,
                'ip' => $ipAddress,
            ]);
        }

        return [
            'rating' => \array_merge(
                $this->shareRatingsRepository->getRatingStatistics($id),
                ['already_rated' => $this->hasAlreadyRated($ipAddress, $id)]
            ),
        ];
    }

    /**
     * @param int    $shareId
     * @param int    $stars
     * @param string $ipAddress
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function canSaveRating(int $shareId, int $stars, string $ipAddress): bool
    {
        if (!($stars >= 1 && $stars <= 5)) {
            return false;
        }
        if ($this->shareRepository->resultExistsById($shareId) === false) {
            return false;
        }
        if ($this->hasAlreadyRated($ipAddress, $shareId) === true) {
            return false;
        }

        return true;
    }

    /**
     * @param string $ipAddress
     * @param int    $shareId
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function hasAlreadyRated(string $ipAddress, int $shareId): bool
    {
        return $this->shareRatingsRepository->hasAlreadyRated($ipAddress, $shareId) === true;
    }
}
