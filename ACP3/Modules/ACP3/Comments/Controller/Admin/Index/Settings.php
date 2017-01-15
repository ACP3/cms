<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Comments\Controller\Admin\Index
 */
class Settings extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Modules\ACP3\Comments\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Comments\Validation\AdminSettingsFormValidation $adminSettingsFormValidation,
        Core\Helpers\FormToken $formTokenHelper
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->formTokenHelper = $formTokenHelper;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $settings = $this->config->getSettings(Comments\Installer\Schema::MODULE_NAME);

        if ($this->modules->isActive('emoticons') === true) {
            $this->view->assign(
                'allow_emoticons',
                $this->formsHelper->yesNoCheckboxGenerator('emoticons', $settings['emoticons'])
            );
        }

        return [
            'dateformat' => $this->get('core.helpers.date')->dateFormatDropdown($settings['dateformat']),
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
            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'dateformat' => $this->get('core.helpers.secure')->strEncode($formData['dateformat']),
                'emoticons' => $formData['emoticons'],
            ];

            return $this->config->saveSettings($data, Comments\Installer\Schema::MODULE_NAME);
        });
    }
}
