<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Contact;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Contact\Controller\Admin\Index
 */
class Index extends Core\Controller\AbstractAdminAction
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
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext                        $context
     * @param \ACP3\Core\Helpers\FormToken                                      $formTokenHelper
     * @param \ACP3\Modules\ACP3\Contact\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Contact\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->count() !== 0) {
            return $this->indexPost($this->request->getPost()->all());
        }

        $settings = $this->config->getSettings('contact');

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
    protected function indexPost(array $formData)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData) {
            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'address' => $this->get('core.helpers.secure')->strEncode($formData['address'], true),
                'ceo' => $this->get('core.helpers.secure')->strEncode($formData['ceo']),
                'disclaimer' => $this->get('core.helpers.secure')->strEncode($formData['disclaimer'], true),
                'fax' => $this->get('core.helpers.secure')->strEncode($formData['fax']),
                'mail' => $formData['mail'],
                'telephone' => $this->get('core.helpers.secure')->strEncode($formData['telephone']),
                'vat_id' => $this->get('core.helpers.secure')->strEncode($formData['vat_id'], true),
            ];

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $this->config->setSettings($data, 'contact');
        });
    }
}
