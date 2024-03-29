<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\EventListener;

use ACP3\Core\Settings\Event\SettingsSaveEvent;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Share\Installer\Schema;
use ACP3\Modules\ACP3\Share\Model\ShareModel;
use ACP3\Modules\ACP3\Share\Repository\ShareRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdateServicesOnSettingsSaveBeforeListener implements EventSubscriberInterface
{
    public function __construct(private readonly SettingsInterface $settings, private readonly ShareModel $shareModel, private readonly ShareRepository $shareRepository)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(SettingsSaveEvent $event): void
    {
        $diff = $this->getRemovedServices($event);

        foreach ($this->shareRepository->getAll() as $result) {
            $services = unserialize($result['services'], ['allowed_classes' => false]);

            if (!empty($services) && \is_array($services)) {
                $data = [
                    'share_services' => array_values(
                        array_diff($services, $diff)
                    ),
                ];
                $this->shareModel->save($data, $result['id']);
            }
        }
    }

    /**
     * @return string[]
     */
    private function getRemovedServices(SettingsSaveEvent $event): array
    {
        $currentSettings = $this->settings->getSettings(Schema::MODULE_NAME);
        $newSettings = $event->getData();
        $currentServices = unserialize($currentSettings['services'], ['allowed_classes' => false]);

        return array_diff(
            \is_array($currentServices) ? $currentServices : [],
            unserialize($newSettings['services'], ['allowed_classes' => false])
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'share.settings.save_before' => '__invoke',
        ];
    }
}
