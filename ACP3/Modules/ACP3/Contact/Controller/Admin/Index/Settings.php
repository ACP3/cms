<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Contact;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Contact\Controller\Admin\Index
 */
class Settings extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Contact\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;
    /**
     * @var Core\Helpers\Secure
     */
    protected $secureHelper;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Core\Helpers\Secure $secureHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Modules\ACP3\Contact\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Secure $secureHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Contact\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->secureHelper = $secureHelper;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->count() !== 0) {
            return $this->executePost($this->request->getPost()->all());
        }

        $settings = $this->config->getSettings(Contact\Installer\Schema::MODULE_NAME);

        return [
            'form' => array_merge($settings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData) {
            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'address' => $this->secureHelper->strEncode($formData['address'], true),
                'ceo' => $this->secureHelper->strEncode($formData['ceo']),
                'disclaimer' => $this->secureHelper->strEncode($formData['disclaimer'], true),
                'fax' => $this->secureHelper->strEncode($formData['fax']),
                'mail' => $formData['mail'],
                'mobile_phone' => $this->secureHelper->strEncode($formData['mobile_phone']),
                'picture_credits' => $this->secureHelper->strEncode($formData['picture_credits'], true),
                'telephone' => $this->secureHelper->strEncode($formData['telephone']),
                'vat_id' => $this->secureHelper->strEncode($formData['vat_id']),
            ];

            return $this->config->saveSettings($data, Contact\Installer\Schema::MODULE_NAME);
        });
    }
}
