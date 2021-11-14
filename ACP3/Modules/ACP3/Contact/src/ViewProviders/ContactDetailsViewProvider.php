<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\ViewProviders;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Contact\Installer\Schema as ContactSchema;

class ContactDetailsViewProvider
{
    public function __construct(private RequestInterface $request, private SettingsInterface $settings, private Steps $breadcrumb, private Translator $translator)
    {
    }

    public function __invoke(): array
    {
        $this->breadcrumb->append(
            $this->translator->t('contact', 'frontend_index_imprint'),
            $this->request->getQuery()
        );

        return [
            'contact' => $this->settings->getSettings(ContactSchema::MODULE_NAME),
            'powered_by' => $this->translator->t(
                'contact',
                'powered_by',
                [
                    '%ACP3%' => '<a href="https://www.acp3-cms.net" target="_blank">ACP3 CMS</a>',
                ]
            ),
        ];
    }
}
