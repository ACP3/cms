<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\EventListener;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Share\Helpers\SocialServices;
use ACP3\Modules\ACP3\Share\Installer\Schema;
use ACP3\Modules\ACP3\Share\Model\Repository\ShareRatingsRepository;
use ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddSocialSharingListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
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

    public function __construct(
        Modules $modules,
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
        $this->modules = $modules;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(TemplateEvent $event): void
    {
        if ($this->modules->isInstalled(Schema::MODULE_NAME) === false) {
            return;
        }

        if ($this->request->getArea() === AreaEnum::AREA_FRONTEND) {
            $sharingInfo = $this->shareRepository->getOneByUri($this->request->getUriWithoutPages());

            $sharing = [];
            if (!empty($sharingInfo)) {
                $sharing['ratings_active'] = ((int) $sharingInfo['ratings_active']) === 1;
                $sharing['rating'] = $this->shareRatingsRepository->getRatingStatistics($sharingInfo['id']);
                $sharing['rating']['share_id'] = $sharingInfo['id'];

                if (((int) $sharingInfo['active']) === 1) {
                    $sharing['path'] = $this->request->getUriWithoutPages();
                    $sharing['services'] = $this->socialServices->getActiveServices();
                }
            }

            $this->view->assign('sharing', $sharing);
            $event->addContent($this->view->fetchTemplate('Share/Partials/add_social_sharing.tpl'));
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'share.layout.add_social_sharing' => '__invoke',
        ];
    }
}
