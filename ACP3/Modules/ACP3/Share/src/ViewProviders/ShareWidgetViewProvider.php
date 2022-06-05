<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\ViewProviders;

use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Share\Helpers\SocialServices;
use ACP3\Modules\ACP3\Share\Repository\ShareRepository;

class ShareWidgetViewProvider
{
    public function __construct(private readonly ShareRepository $shareRepository, private readonly SocialServices $socialServices, private readonly Translator $translator)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(string $path): array
    {
        $path = urldecode($path);
        $sharingInfo = $this->shareRepository->getOneByUri($path);

        return [
            'shariff' => [
                'lang' => $this->translator->getShortIsoCode(),
                'path' => $path,
                'services' => $this->getServices($sharingInfo),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $sharingInfo
     *
     * @return string[]
     */
    private function getServices(array $sharingInfo): array
    {
        $services = [];
        if (!empty($sharingInfo['services'])) {
            $services = unserialize($sharingInfo['services']);
        }
        if (empty($services)) {
            $services = $this->socialServices->getActiveServices();
        }

        return array_values($services);
    }
}
