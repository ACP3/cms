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
use ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository;

class ShareFormFields
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository
     */
    private $shareRepository;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialServices
     */
    private $socialServices;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    /**
     * SharingInfoFormFields constructor.
     *
     * @param \ACP3\Core\Settings\SettingsInterface                     $settings
     * @param \ACP3\Core\I18n\Translator                                $translator
     * @param \ACP3\Core\Helpers\Forms                                  $formsHelper
     * @param \ACP3\Modules\ACP3\Share\Helpers\SocialServices           $socialServices
     * @param \ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository $shareRepository
     */
    public function __construct(
        SettingsInterface $settings,
        Translator $translator,
        Forms $formsHelper,
        SocialServices $socialServices,
        ShareRepository $shareRepository)
    {
        $this->formsHelper = $formsHelper;
        $this->shareRepository = $shareRepository;
        $this->translator = $translator;
        $this->socialServices = $socialServices;
        $this->settings = $settings;
    }

    /**
     * Returns the sharing form fields.
     *
     * @param string $path
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
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
     * @param string $path
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getData(string $path): array
    {
        $sharingInfo = [
            'active' => 0,
            'customize_services' => 0,
            'ratings_active' => 0,
        ];

        if (!empty($path)) {
            $path .= !\preg_match('/\/$/', $path) ? '/' : '';

            $sharingInfo = \array_merge(
                $sharingInfo,
                $this->shareRepository->getOneByUri($path)
            );
        }

        if (empty($sharingInfo['services'])) {
            $settings = $this->settings->getSettings(Schema::MODULE_NAME);

            $sharingInfo['services'] = $settings['services'];
        }

        $sharingInfo['services'] = \unserialize($sharingInfo['services']);

        if (\is_array($sharingInfo['services']) === false) {
            $sharingInfo['services'] = [];
        }

        return $sharingInfo;
    }

    /**
     * @param array $services
     *
     * @return bool
     */
    private function hasCustomizedServices(array $services): bool
    {
        return !empty($services) && $services !== $this->socialServices->getActiveServices();
    }

    /**
     * @return array
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
     * @param array $services
     *
     * @return array
     */
    private function getCurrentServices(array $services): array
    {
        return $services ?: $this->socialServices->getActiveServices();
    }
}
