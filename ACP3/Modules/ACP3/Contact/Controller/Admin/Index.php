<?php

namespace ACP3\Modules\ACP3\Contact\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Contact;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Contact\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Contact\Validator
     */
    protected $contactValidator;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\Helpers\FormToken               $formTokenHelper
     * @param \ACP3\Modules\ACP3\Contact\Validator       $contactValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Contact\Validator $contactValidator)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->contactValidator = $contactValidator;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionIndex()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_indexPost($this->request->getPost()->getAll());
        }

        $settings = $this->config->getSettings('contact');

        $this->view->assign('form', array_merge($settings, $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _indexPost(array $formData)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData) {
            $this->contactValidator->validateSettings($formData);

            $data = [
                'address' => Core\Functions::strEncode($formData['address'], true),
                'ceo' => Core\Functions::strEncode($formData['ceo']),
                'disclaimer' => Core\Functions::strEncode($formData['disclaimer'], true),
                'fax' => Core\Functions::strEncode($formData['fax']),
                'mail' => $formData['mail'],
                'telephone' => Core\Functions::strEncode($formData['telephone']),
                'vat_id' => Core\Functions::strEncode($formData['vat_id'], true),
            ];

            $this->formTokenHelper->unsetFormToken();

            return $this->config->setSettings($data, 'contact');
        });
    }
}
