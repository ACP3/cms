<?php
/**
 * Created by PhpStorm.
 * User: goratsch
 * Date: 21.06.14
 * Time: 22:51
 */

namespace ACP3\Modules\Comments;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\Comments
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Core\Auth
     */
    protected $auth;
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var Model
     */
    protected $commentsModel;

    public function __construct(Core\Lang $lang, Core\Auth $auth, Core\Date $date, Model $commentsModel)
    {
        parent::__construct($lang);

        $this->auth = $auth;
        $this->date = $date;
        $this->commentsModel = $commentsModel;
    }

    /**
     * @param array $formData
     * @param $ip
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData, $ip)
    {
        $this->validateFormKey();

        // Flood Sperre
        $flood = $this->commentsModel->getLastDateFromIp($ip);
        $floodTime = !empty($flood) ? $this->date->timestamp($flood, true) + 30 : 0;
        $time = $this->date->timestamp('now', true);

        $errors = array();
        if ($floodTime > $time) {
            $errors[] = sprintf($this->lang->t('system', 'flood_no_entry_possible'), $floodTime - $time);
        }
        if (empty($formData['name'])) {
            $errors['name'] = $this->lang->t('system', 'name_to_short');
        }
        if (strlen($formData['message']) < 3) {
            $errors['message'] = $this->lang->t('system', 'message_to_short');
        }
        if (Core\Modules::hasPermission('frontend/captcha/index/image') === true && $this->auth->isUser() === false && Core\Validate::captcha($formData['captcha']) === false) {
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
    public function validateEdit(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if ((empty($comment['user_id']) || Core\Validate::isNumber($comment['user_id']) === false) && empty($formData['name'])) {
            $errors['name'] = $this->lang->t('system', 'name_to_short');
        }
        if (strlen($formData['message']) < 3) {
            $errors['message'] = $this->lang->t('system', 'message_to_short');
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
        if (Core\Modules::isActive('emoticons') === true && (!isset($formData['emoticons']) || ($formData['emoticons'] != 0 && $formData['emoticons'] != 1))) {
            $errors[] = $this->lang->t('comments', 'select_emoticons');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }


} 