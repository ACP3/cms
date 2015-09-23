<?php
namespace ACP3\Modules\ACP3\Newsletter;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Newsletter
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Validator\Rules\Captcha
     */
    protected $captchaValidator;
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\User
     */
    protected $user;
    /**
     * @var Model
     */
    protected $newsletterModel;

    /**
     * @param Core\Lang                    $lang
     * @param Core\Validator\Rules\Misc    $validate
     * @param Core\Validator\Rules\Captcha $captchaValidator
     * @param Core\ACL                     $acl
     * @param Core\User                    $user
     * @param Model                        $newsletterModel
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Captcha $captchaValidator,
        Core\ACL $acl,
        Core\User $user,
        Model $newsletterModel
    )
    {
        parent::__construct($lang, $validate);

        $this->captchaValidator = $captchaValidator;
        $this->acl = $acl;
        $this->user = $user;
        $this->newsletterModel = $newsletterModel;
    }

    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (strlen($formData['title']) < 3) {
            $this->errors['title'] = $this->lang->t('newsletter', 'subject_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $this->errors['text'] = $this->lang->t('newsletter', 'text_to_short') . strlen($formData['text']);
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSubscribe(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (!empty($formData['salutation']) && !in_array($formData['salutation'], [1, 2])) {
            $this->errors['salutation'] = $this->lang->t('newsletter', 'select_salutation');
        }
        if ($this->validate->email($formData['mail']) === false) {
            $this->errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        } elseif ($this->newsletterModel->accountExists($formData['mail']) === true) {
            $this->errors['mail'] = $this->lang->t('newsletter', 'account_exists');
        }
        if ($this->acl->hasPermission('frontend/captcha/index/image') === true &&
            $this->user->isAuthenticated() === false &&
            $this->captchaValidator->captcha($formData['captcha']) === false) {
            $this->errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateUnsubscribe(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if ($this->validate->email($formData['mail']) === false) {
            $this->errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        } elseif ($this->newsletterModel->accountExists($formData['mail']) === false) {
            $this->errors['mail'] = $this->lang->t('newsletter', 'account_not_exists');
        }
        if ($this->acl->hasPermission('frontend/captcha/index/image') === true && $this->user->isAuthenticated() === false && $this->captchaValidator->captcha($formData['captcha']) === false) {
            $this->errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if ($this->validate->email($formData['mail']) === false) {
            $this->errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param string $hash
     *
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateActivate($hash)
    {
        $this->errors = [];
        if ($this->newsletterModel->accountExistsByHash($hash) === false) {
            $this->errors[] = $this->lang->t('newsletter', 'account_not_exists');
        }

        $this->_checkForFailedValidation();
    }
}
