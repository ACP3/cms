<?php
namespace ACP3\Modules\Newsletter;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\Newsletter
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
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var Model
     */
    protected $newsletterModel;

    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Captcha $captchaValidator,
        Core\Auth $auth,
        Core\Modules $modules,
        Model $newsletterModel
    )
    {
        parent::__construct($lang, $validate);

        $this->captchaValidator = $captchaValidator;
        $this->auth = $auth;
        $this->modules = $modules;
        $this->newsletterModel = $newsletterModel;
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('newsletter', 'subject_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $this->lang->t('newsletter', 'text_to_short') . strlen($formData['text']);
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateSubscribe(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if ($this->validate->email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if ($this->validate->email($formData['mail']) && $this->newsletterModel->accountExists($formData['mail']) === true) {
            $errors['mail'] = $this->lang->t('newsletter', 'account_exists');
        }
        if ($this->modules->hasPermission('frontend/captcha/index/image') === true && $this->auth->isUser() === false && $this->captchaValidator->captcha($formData['captcha']) === false) {
            $errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateUnsubscribe(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if ($this->validate->email($formData['mail']) === false) {
            $errors[] = $this->lang->t('system', 'wrong_email_format');
        }
        if ($this->validate->email($formData['mail']) && $this->newsletterModel->accountExists($formData['mail']) === false) {
            $errors[] = $this->lang->t('newsletter', 'account_not_exists');
        }
        if ($this->modules->hasPermission('frontend/captcha/index/image') === true && $this->auth->isUser() === false && $this->captchaValidator->captcha($formData['captcha']) === false) {
            $errors[] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if ($this->validate->email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param $mail
     * @param $hash
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateActivate($mail, $hash)
    {
        $errors = array();
        if ($this->newsletterModel->accountExists($mail, $hash) === false) {
            $errors[] = $this->lang->t('newsletter', 'account_not_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

}