<?php
namespace ACP3\Modules\Guestbook;

use ACP3\Core;
use ACP3\Modules\Newsletter;

/**
 * Class Validator
 * @package ACP3\Modules\Guestbook
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
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var Model
     */
    protected $guestbookModel;
    /**
     * @var Newsletter\Model
     */
    protected $newsletterModel;

    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Captcha $captchaValidator,
        Core\Auth $auth,
        Core\Date $date,
        Core\Modules $modules,
        Model $guestbookModel,
        Newsletter\Model $newsletterModel
    )
    {
        parent::__construct($lang, $validate);

        $this->captchaValidator = $captchaValidator;
        $this->auth = $auth;
        $this->date = $date;
        $this->modules = $modules;
        $this->guestbookModel = $guestbookModel;
        $this->newsletterModel = $newsletterModel;
    }

    /**
     * @param array $formData
     * @param $newsletterAccess
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData, $newsletterAccess)
    {
        $this->validateFormKey();

        // Flood Sperre
        $flood = $this->guestbookModel->getLastDateFromIp($_SERVER['REMOTE_ADDR']);
        $floodTime = !empty($flood) ? $this->date->timestamp($flood, true) + 30 : 0;
        $time = $this->date->timestamp('now', true);

        $errors = array();
        if ($floodTime > $time) {
            $errors[] = sprintf($this->lang->t('system', 'flood_no_entry_possible'), $floodTime - $time);
        }
        if (empty($formData['name'])) {
            $errors['name'] = $this->lang->t('system', 'name_to_short');
        }
        if (!empty($formData['mail']) && $this->validate->email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if (strlen($formData['message']) < 3) {
            $errors['message'] = $this->lang->t('system', 'message_to_short');
        }
        if ($this->modules->hasPermission('frontend/captcha/index/image') === true && $this->auth->isUser() === false && $this->captchaValidator->captcha($formData['captcha']) === false) {
            $errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }
        if ($newsletterAccess === true && isset($formData['subscribe_newsletter']) && $formData['subscribe_newsletter'] == 1) {
            if ($this->validate->email($formData['mail']) === false) {
                $errors['mail'] = $this->lang->t('guestbook', 'type_in_email_address_to_subscribe_to_newsletter');
            }
            if ($this->validate->email($formData['mail']) === true && $this->newsletterModel->accountExists($formData['mail']) === true) {
                $errors[] = $this->lang->t('newsletter', 'account_exists');
            }
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @param array $settings
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEdit(array $formData, array $settings)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['name'])) {
            $errors['name'] = $this->lang->t('system', 'name_to_short');
        }
        if (strlen($formData['message']) < 3) {
            $errors['message'] = $this->lang->t('system', 'message_to_short');
        }
        if ($settings['notify'] == 2 && (!isset($formData['active']) || ($formData['active'] != 0 && $formData['active'] != 1))) {
            $errors['notify'] = $this->lang->t('guestbook', 'select_activate');
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
        if (empty($formData['dateformat']) || ($formData['dateformat'] !== 'long' && $formData['dateformat'] !== 'short')) {
            $errors['dateformat'] = $this->lang->t('system', 'select_date_format');
        }
        if (!isset($formData['notify']) || ($formData['notify'] != 0 && $formData['notify'] != 1 && $formData['notify'] != 2)) {
            $errors['notify'] = $this->lang->t('guestbook', 'select_notification_type');
        }
        if ($formData['notify'] != 0 && $this->validate->email($formData['notify_email']) === false) {
            $errors['notify-email'] = $this->lang->t('system', 'wrong_email_format');
        }
        if (!isset($formData['overlay']) || $formData['overlay'] != 1 && $formData['overlay'] != 0) {
            $errors[] = $this->lang->t('guestbook', 'select_use_overlay');
        }
        if ($this->modules->isActive('emoticons') === true && (!isset($formData['emoticons']) || ($formData['emoticons'] != 0 && $formData['emoticons'] != 1))) {
            $errors[] = $this->lang->t('guestbook', 'select_emoticons');
        }
        if ($this->modules->isActive('newsletter') === true && (!isset($formData['newsletter_integration']) || ($formData['newsletter_integration'] != 0 && $formData['newsletter_integration'] != 1))) {
            $errors[] = $this->lang->t('guestbook', 'select_newsletter_integration');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

} 