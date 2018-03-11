<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Event\Listener;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Share\Helpers\SocialServices;
use ACP3\Modules\ACP3\Share\Model\Repository\ShareRatingsRepository;
use ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository;

class AddSocialSharingListener
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialServices
     */
    private $socialServices;
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository
     */
    private $shareRepository;
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\Repository\ShareRatingsRepository
     */
    private $shareRatingsRepository;

    /**
     * AddSocialSharingListener constructor.
     *
     * @param \ACP3\Core\Http\RequestInterface                                 $request
     * @param \ACP3\Core\View                                                  $view
     * @param \ACP3\Modules\ACP3\Share\Helpers\SocialServices                  $socialServices
     * @param \ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository        $shareRepository
     * @param \ACP3\Modules\ACP3\Share\Model\Repository\ShareRatingsRepository $shareRatingsRepository
     */
    public function __construct(
        RequestInterface $request,
        View $view,
        SocialServices $socialServices,
        ShareRepository $shareRepository,
        ShareRatingsRepository $shareRatingsRepository)
    {
        $this->request = $request;
        $this->view = $view;
        $this->socialServices = $socialServices;
        $this->shareRepository = $shareRepository;
        $this->shareRatingsRepository = $shareRatingsRepository;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(): void
    {
        if ($this->request->getArea() === AreaEnum::AREA_FRONTEND) {
            $sharingInfo = $this->shareRepository->getOneByUri($this->request->getUriWithoutPages());

            $sharing = [];
            if (!empty($sharingInfo)) {
                $sharing['ratings_active'] = ((int) $sharingInfo['ratings_active']) === 1;
                $sharing['rating'] = $this->shareRatingsRepository->getRatingStatistics($sharingInfo['id']);

                if (((int) $sharingInfo['active']) === 1) {
                    $sharing['path'] = $this->request->getUriWithoutPages();
                    $sharing['services'] = $this->socialServices->getActiveServices();
                }
            }

            $this->view->assign('sharing', $sharing);
            $this->view->displayTemplate('Share/Partials/add_social_sharing.tpl');
        }
    }
}
