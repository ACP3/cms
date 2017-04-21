<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index
 */
class Settings extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\FormBlockInterface $block
     * @param \ACP3\Modules\ACP3\Emoticons\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $block,
        Emoticons\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->block = $block;
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
                'width' => (int)$formData['width'],
                'height' => (int)$formData['height'],
                'filesize' => (int)$formData['filesize'],
            ];

            return $this->config->saveSettings($data, Emoticons\Installer\Schema::MODULE_NAME);
        });
    }
}
