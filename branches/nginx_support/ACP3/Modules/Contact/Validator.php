<?php

namespace ACP3\Modules\Contact;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\Contact
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Validator\Rules\Captcha
     */
    protected $captchaValidator;
    /**
     * @var \ACP3\Core\Auth
     */
    protected $auth;
    /**
     * @var Core\ACL
     */
    protected $acl;

    /**
     * @param Core\Lang $lang
     * @param Core\Validator\Rules\Misc $validate
     * @param Core\Validator\Rules\Captcha $captchaValidator
     * @param Core\ACL $acl
     * @param Core\Auth $auth
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Captcha $captchaValidator,
        Core\ACL $acl,
        Core\Auth $auth
    ) {
        parent::__construct($lang, $validate);

        $this->captchaValidator = $captchaValidator;
        $this->acl = $acl;
        $this->auth = $auth;
    }

    /**
     * @param array $formData
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['name'])) {
            $this->errors['name'] = $this->lang->t('system', 'name_to_short');
        }
        if ($this->validate->email($formData['mail']) === false) {
            $this->errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if (strlen($formData['message']) < 3) {
            $this->errors['message'] = $this->lang->t('system', 'message_to_short');
        }
        if ($this->acl->hasPermission('frontend/captcha/index/image') === true &&
            $this->auth->isUser() === false && $this->captchaValidator->captcha($formData['captcha']) === false
        ) {
            $this->errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (!empty($formData['mail']) && $this->validate->email($formData['mail']) === false) {
            $this->errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }

        $this->_checkForFailedValidation();
    }
}
