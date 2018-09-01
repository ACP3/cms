<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Event\Listener;

use ACP3\Core\Settings\Event\SettingsSaveEvent;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Share\Installer\Schema;
use ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository;
use ACP3\Modules\ACP3\Share\Model\ShareModel;

class UpdateServicesOnSettingsSaveBeforeListener
{
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\ShareModel
     */
    private $shareModel;
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository
     */
    private $shareRepository;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    /**
     * UpdateServicesOnSettingsSaveAfterListener constructor.
     *
     * @param \ACP3\Core\Settings\SettingsInterface                     $settings
     * @param \ACP3\Modules\ACP3\Share\Model\ShareModel                 $shareModel
     * @param \ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository $shareRepository
     */
    public function __construct(
        SettingsInterface $settings,
        ShareModel $shareModel,
        ShareRepository $shareRepository)
    {
        $this->shareModel = $shareModel;
        $this->shareRepository = $shareRepository;
        $this->settings = $settings;
    }

    /**
     * @param \ACP3\Core\Settings\Event\SettingsSaveEvent $event
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(SettingsSaveEvent $event): void
    {
        $diff = $this->getRemovedServices($event);

        foreach ($this->shareRepository->getAll() as $result) {
            $services = \unserialize($result['services']);

            if (!empty($services) && \is_array($services)) {
                $data = [
                    'share_services' => \array_values(
                        \array_diff($services, $diff)
                    ),
                ];
                $this->shareModel->save($data, $result['id']);
            }
        }
    }

    /**
     * @param \ACP3\Core\Settings\Event\SettingsSaveEvent $event
     *
     * @return array
     */
    private function getRemovedServices(SettingsSaveEvent $event): array
    {
        $currentSettings = $this->settings->getSettings(Schema::MODULE_NAME);
        $newSettings = $event->getData();
        $currentServices = \unserialize($currentSettings['services']);

        return \array_diff(
            \is_array($currentServices) ? $currentServices : [],
            \unserialize($newSettings['services'])
        );
    }
}
