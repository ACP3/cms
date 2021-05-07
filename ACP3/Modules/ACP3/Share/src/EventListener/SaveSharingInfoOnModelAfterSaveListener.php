<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager;
use ACP3\Modules\ACP3\Share\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SaveSharingInfoOnModelAfterSaveListener implements EventSubscriberInterface
{
    /**
     * @var ACL
     */
    private $acl;
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager
     */
    private $socialSharingManager;

    public function __construct(
        ACL $acl,
        SocialSharingManager $socialSharingManager
    ) {
        $this->acl = $acl;
        $this->socialSharingManager = $socialSharingManager;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(ModelSaveEvent $event)
    {
        if ($this->acl->hasPermission('admin/share/index/create')) {
            $formData = $event->getRawData();

            if ($event->getModuleName() !== Schema::MODULE_NAME && !empty($formData['share_uri_pattern'])) {
                $this->socialSharingManager->saveSharingInfo(
                    sprintf($formData['share_uri_pattern'], $event->getEntryId()),
                    $formData['share_active'],
                    $formData['share_customize_services'] == 1 ? $formData['share_services'] : [],
                    $formData['share_ratings_active']
                );
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'core.model.after_save' => '__invoke',
        ];
    }
}
