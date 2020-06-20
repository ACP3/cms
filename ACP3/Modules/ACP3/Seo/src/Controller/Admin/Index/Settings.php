<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Seo;

class Settings extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Validation\AdminSettingsFormValidation
     */
    private $adminSettingsFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    private $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\ViewProviders\AdminSettingsViewProvider
     */
    private $adminSettingsViewProvider;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Action $actionHelper,
        Core\Helpers\Secure $secureHelper,
        Seo\Validation\AdminSettingsFormValidation $adminSettingsFormValidation,
        Seo\ViewProviders\AdminSettingsViewProvider $adminSettingsViewProvider
    ) {
        parent::__construct($context);

        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->secureHelper = $secureHelper;
        $this->adminSettingsViewProvider = $adminSettingsViewProvider;
        $this->actionHelper = $actionHelper;
    }

    public function execute(): array
    {
        return ($this->adminSettingsViewProvider)();
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost()
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();

            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'meta_description' => $this->secureHelper->strEncode($formData['meta_description']),
                'meta_keywords' => $this->secureHelper->strEncode($formData['meta_keywords']),
                'robots' => (int) $formData['robots'],
                'sitemap_is_enabled' => (int) $formData['sitemap_is_enabled'],
                'sitemap_save_mode' => (int) $formData['sitemap_save_mode'],
                'sitemap_separate' => (int) $formData['sitemap_separate'],
            ];

            return $this->config->saveSettings($data, Seo\Installer\Schema::MODULE_NAME);
        });
    }
}
