<?php

namespace ACP3\Modules\Contact;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author goratsch
 */
class Model extends Core\Model
{

    /**
     * @var \ACP3\Core\Auth
     */
    protected $auth;

    public function __construct(\Doctrine\DBAL\Connection $db, Core\Lang $lang, Core\Auth $auth)
    {
        parent::__construct($db, $lang);

        $this->auth = $auth;
    }

    public function validate(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['name'])) {
            $errors['name'] = $this->lang->t('system', 'name_to_short');
        }
        if (Core\Validate::email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if (strlen($formData['message']) < 3) {
            $errors['message'] = $this->lang->t('system', 'message_to_short');
        }
        if (Core\Modules::hasPermission('captcha', 'image') === true &&
            $this->auth->isUser() === false && Core\Validate::captcha($formData['captcha']) === false) {
            $errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (!empty($formData['mail']) && Core\Validate::email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }
}
