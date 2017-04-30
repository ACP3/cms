<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

class Settings extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;
    /**
     * @var Core\View\Block\SettingsFormBlockInterface
     */
    private $block;
    /**
     * @var Core\Helpers\Secure
     */
    private $secure;

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\SettingsFormBlockInterface $block
     * @param Core\Helpers\Secure $secure
     * @param \ACP3\Modules\ACP3\Seo\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\SettingsFormBlockInterface $block,
        Core\Helpers\Secure $secure,
        Seo\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->block = $block;
        $this->secure = $secure;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return $this->block
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();

            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'meta_description' => $this->secure->strEncode($formData['meta_description']),
                'meta_keywords' => $this->secure->strEncode($formData['meta_keywords']),
                'robots' => (int)$formData['robots'],
                'sitemap_is_enabled' => (int)$formData['sitemap_is_enabled'],
                'sitemap_save_mode' => (int)$formData['sitemap_save_mode'],
                'sitemap_separate' => (int)$formData['sitemap_separate']
            ];

            return $this->config->saveSettings($data, Seo\Installer\Schema::MODULE_NAME);
        });
    }
}
