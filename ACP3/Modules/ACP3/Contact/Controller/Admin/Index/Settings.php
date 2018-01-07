<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Contact;

class Settings extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Contact\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;
    /**
     * @var Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Core\View\Block\SettingsFormBlockInterface
     */
    private $block;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                     $context
     * @param Core\View\Block\SettingsFormBlockInterface                        $block
     * @param Core\Helpers\Secure                                               $secureHelper
     * @param \ACP3\Modules\ACP3\Contact\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\SettingsFormBlockInterface $block,
        Core\Helpers\Secure $secureHelper,
        Contact\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->secureHelper = $secureHelper;
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
