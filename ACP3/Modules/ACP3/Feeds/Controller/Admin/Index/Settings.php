<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Feeds;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Feeds\Controller\Admin\Index
 */
class Settings extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Feeds\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var Core\Helpers\Secure
     */
    protected $secureHelper;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\Helpers\Secure $secureHelper
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Modules\ACP3\Feeds\Validation\AdminFormValidation $adminFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Secure $secureHelper,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Feeds\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->adminFormValidation = $adminFormValidation;
        $this->secureHelper = $secureHelper;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $settings = $this->config->getSettings(Feeds\Installer\Schema::MODULE_NAME);

        $feedTypes = [
            'RSS 1.0' => 'RSS 1.0',
            'RSS 2.0' => 'RSS 2.0',
            'ATOM' => 'ATOM'
        ];

        return [
            'feed_types' => $this->formsHelper->choicesGenerator('feed_type', $feedTypes, $settings['feed_type']),
            'form' => array_merge($settings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();
            $this->adminFormValidation->validate($formData);

            $data = [
                'feed_image' => $this->secureHelper->strEncode($formData['feed_image']),
                'feed_type' => $formData['feed_type']
            ];

            return $this->config->saveSettings($data, Feeds\Installer\Schema::MODULE_NAME);
        });
    }
}
