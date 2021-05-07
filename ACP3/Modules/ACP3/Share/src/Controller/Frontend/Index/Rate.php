<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Frontend\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Controller\InvokableActionInterface;
use ACP3\Modules\ACP3\Share\Model\Repository\ShareRatingsRepository;
use ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository;
use ACP3\Modules\ACP3\Share\Model\ShareRatingModel;

class Rate extends AbstractWidgetAction implements InvokableActionInterface
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
     * @var bool|null
     */
    private $alreadyRated;

    public function __construct(
        WidgetContext $context,
        ShareRepository $shareRepository,
        ShareRatingsRepository $shareRatingsRepository,
        ShareRatingModel $shareRatingModel
    ) {
        parent::__construct($context);

        $this->shareRepository = $shareRepository;
        $this->shareRatingsRepository = $shareRatingsRepository;
        $this->shareRatingModel = $shareRatingModel;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id): array
    {
        $stars = (int) $this->request->getPost()->get('stars', 0);
        $ipAddress = $this->request->getSymfonyRequest()->getClientIp();

        if ($this->canSaveRating($id, $stars, $ipAddress) === true) {
            $this->shareRatingModel->save([
                'share_id' => $id,
                'stars' => $stars,
                'ip' => $ipAddress,
            ]);
        }

        return [
            'rating' => array_merge(
                $this->shareRatingsRepository->getRatingStatistics($id),
                [
                    'share_id' => $id,
                    'already_rated' => $this->hasAlreadyRated($ipAddress, $id),
                ]
            ),
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Exception
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
     * @throws \Doctrine\DBAL\Exception
     */
    private function hasAlreadyRated(string $ipAddress, int $shareId): bool
    {
        if ($this->alreadyRated === null) {
            $this->alreadyRated = $this->shareRatingsRepository->hasAlreadyRated($ipAddress, $shareId);
        }

        return $this->alreadyRated;
    }
}
