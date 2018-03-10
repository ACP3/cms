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
use ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository;

class AddShariffOnLayoutContentAfterListener
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
     * AddShariffOnLayoutContentAfterListener constructor.
     *
     * @param \ACP3\Core\Http\RequestInterface                          $request
     * @param \ACP3\Core\View                                           $view
     * @param \ACP3\Modules\ACP3\Share\Helpers\SocialServices           $socialServices
     * @param \ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository $shareRepository
     */
    public function __construct(
        RequestInterface $request,
        View $view,
        SocialServices $socialServices,
        ShareRepository $shareRepository)
    {
        $this->request = $request;
        $this->view = $view;
        $this->socialServices = $socialServices;
        $this->shareRepository = $shareRepository;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(): void
    {
        if ($this->request->getArea() === AreaEnum::AREA_FRONTEND) {
            $item = $this->shareRepository->getOneByUri($this->request->getUriWithoutPages());

            if (!empty($item) && (int) $item['active'] == 1) {
                $this->view->assign('shariff', [
                    'path' => $this->request->getUriWithoutPages(),
                    'services' => \json_encode($this->socialServices->getActiveServices()),
                ]);

                $this->view->displayTemplate('Share/Partials/add_shariff.tpl');
            }
        }
    }
}
