<?php

namespace ACP3\Modules\ACP3\Contact\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Contact;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Contact\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Contact\Validator
     */
    protected $contactValidator;

    /**
     * @param \ACP3\Core\Context\Admin        $context
     * @param \ACP3\Core\Helpers\Secure       $secureHelper
     * @param \ACP3\Modules\ACP3\Contact\Validator $contactValidator
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Helpers\Secure $secureHelper,
        Contact\Validator $contactValidator)
    {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->contactValidator = $contactValidator;
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            $this->_indexPost($_POST);
        }

        $settings = $this->config->getSettings('contact');

        $this->view->assign('form', array_merge($settings, $this->request->getPost()->getAll()));

        $this->secureHelper->generateFormToken($this->request->getQuery());
    }

    /**
     * @param array $formData
     */
    protected function _indexPost(array $formData)
    {
        try {
            $this->contactValidator->validateSettings($formData);

            $data = [
                'address' => Core\Functions::strEncode($formData['address'], true),
                'mail' => $formData['mail'],
                'telephone' => Core\Functions::strEncode($formData['telephone']),
                'fax' => Core\Functions::strEncode($formData['fax']),
                'disclaimer' => Core\Functions::strEncode($formData['disclaimer'], true),
                'vat_id' => Core\Functions::strEncode($formData['vat_id'], true),
            ];

            $bool = $this->config->setSettings($data, 'contact');

            $this->secureHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
