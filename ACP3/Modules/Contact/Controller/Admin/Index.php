<?php

namespace ACP3\Modules\Contact\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Contact;

/**
 * Class Index
 * @package ACP3\Modules\Contact\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Modules\Contact\Validator
     */
    protected $contactValidator;
    /**
     * @var \ACP3\Core\Config
     */
    protected $contactConfig;

    /**
     * @param \ACP3\Core\Context\Admin        $context
     * @param \ACP3\Core\Helpers\Secure       $secureHelper
     * @param \ACP3\Modules\Contact\Validator $contactValidator
     * @param \ACP3\Core\Config               $contactConfig
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Helpers\Secure $secureHelper,
        Contact\Validator $contactValidator,
        Core\Config $contactConfig)
    {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->contactValidator = $contactValidator;
        $this->contactConfig = $contactConfig;
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            $this->_indexPost($_POST);
        }

        $settings = $this->contactConfig->getSettings();

        $this->view->assign('form', array_merge($settings, $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    /**
     * @param array $formData
     */
    private function _indexPost(array $formData)
    {
        try {
            $this->contactValidator->validateSettings($formData);

            $data = [
                'address' => Core\Functions::strEncode($formData['address'], true),
                'mail' => $formData['mail'],
                'telephone' => Core\Functions::strEncode($formData['telephone']),
                'fax' => Core\Functions::strEncode($formData['fax']),
                'disclaimer' => Core\Functions::strEncode($formData['disclaimer'], true),
            ];

            $bool = $this->contactConfig->setSettings($data);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
