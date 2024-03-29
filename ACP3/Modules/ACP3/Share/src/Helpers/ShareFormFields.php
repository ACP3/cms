<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Helpers;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Share\Installer\Schema;
use ACP3\Modules\ACP3\Share\Repository\ShareRepository;

class ShareFormFields
{
    public function __construct(private readonly SettingsInterface $settings, private readonly Translator $translator, private readonly Forms $formsHelper, private readonly SocialServices $socialServices, private readonly ShareRepository $shareRepository)
    {
    }

    /**
     * Returns the sharing form fields.
     *
     * @return array<string, array<string, mixed[]>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function formFields(string $path = ''): array
    {
        $sharingInfo = $this->getData($path);

        return [
            'active' => $this->formsHelper->yesNoCheckboxGenerator(
                'share_active',
                $sharingInfo['active']
            ),
            'customize_services' => $this->formsHelper->yesNoCheckboxGenerator(
                'share_customize_services',
                $this->hasCustomizedServices($sharingInfo['services']) ? 1 : 0
            ),
            'services' => $this->formsHelper->choicesGenerator(
                'share_services',
                $this->getAvailableServices(),
                $this->getCurrentServices($sharingInfo['services'])
            ),
            'ratings_active' => $this->formsHelper->yesNoCheckboxGenerator(
                'share_ratings_active',
                $sharingInfo['ratings_active']
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function getData(string $path): array
    {
        $sharingInfo = [
            'active' => 0,
            'customize_services' => 0,
            'ratings_active' => 0,
        ];

        if (!empty($path)) {
            $path .= !preg_match('/\/$/', $path) ? '/' : '';

            $sharingInfo = array_merge(
                $sharingInfo,
                $this->shareRepository->getOneByUri($path)
            );
        }

        if (empty($sharingInfo['services'])) {
            $settings = $this->settings->getSettings(Schema::MODULE_NAME);

            $sharingInfo['services'] = $settings['services'];
        }

        $sharingInfo['services'] = unserialize($sharingInfo['services'], ['allowed_classes' => false]);

        if (\is_array($sharingInfo['services']) === false) {
            $sharingInfo['services'] = [];
        }

        return $sharingInfo;
    }

    /**
     * @param string[] $services
     */
    private function hasCustomizedServices(array $services): bool
    {
        return !empty($services) && $services !== $this->socialServices->getActiveServices();
    }

    /**
     * @return array<string, string>
     */
    private function getAvailableServices(): array
    {
        $services = [];
        foreach ($this->socialServices->getActiveServices() as $service) {
            $services[$service] = $this->translator->t('share', 'service_' . $service);
        }

        return $services;
    }

    /**
     * @param string[] $services
     *
     * @return string[]
     */
    private function getCurrentServices(array $services): array
    {
        return $services ?: $this->socialServices->getActiveServices();
    }
}
