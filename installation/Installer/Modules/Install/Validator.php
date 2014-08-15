<?php
namespace ACP3\Installer\Modules\Install;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Installer\Modules\Install
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Validator\Rules\Date
     */
    protected $dateValidator;

    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Date $dateValidator
    )
    {
        parent::__construct($lang, $validate);

        $this->dateValidator = $dateValidator;
    }

    /**
     * @param array $formData
     * @param $configFilePath
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateConfiguration(array $formData, $configFilePath)
    {
        $errors = array();
        if (empty($formData['db_host'])) {
            $errors['db-host'] = $this->lang->t('install', 'type_in_db_host');
        }
        if (empty($formData['db_user'])) {
            $errors['db-user'] = $this->lang->t('install', 'type_in_db_username');
        }
        if (empty($formData['db_name'])) {
            $errors['db-name'] = $this->lang->t('install', 'type_in_db_name');
        }
        if (!empty($formData['db_host']) && !empty($formData['db_user']) && !empty($formData['db_name'])) {
            try {
                $config = new \Doctrine\DBAL\Configuration();

                $connectionParams = array(
                    'dbname' => $formData['db_name'],
                    'user' => $formData['db_user'],
                    'password' => $formData['db_password'],
                    'host' => $formData['db_host'],
                    'driver' => 'pdo_mysql',
                    'charset' => 'utf8'
                );
                $db = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
                $db->query('USE `' . $formData['db_name'] . '`');
            } catch (\Exception $e) {
                $errors[] = sprintf($this->lang->t('install', 'db_connection_failed'), $e->getMessage());
            }
        }
        if (empty($formData['user_name'])) {
            $errors['user-name'] = $this->lang->t('install', 'type_in_user_name');
        }
        if ((empty($formData['user_pwd']) || empty($formData['user_pwd_wdh'])) ||
            (!empty($formData['user_pwd']) && !empty($formData['user_pwd_wdh']) && $formData['user_pwd'] != $formData['user_pwd_wdh'])
        ) {
            $errors['user-pwd'] = $this->lang->t('install', 'type_in_pwd');
        }
        if ($this->validate->email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('install', 'wrong_email_format');
        }
        if (empty($formData['date_format_long'])) {
            $errors['date-format-long'] = $this->lang->t('install', 'type_in_date_format');
        }
        if (empty($formData['date_format_short'])) {
            $errors['date-format-short'] = $this->lang->t('install', 'type_in_date_format');
        }
        if ($this->dateValidator->timeZone($formData['date_time_zone']) === false) {
            $errors['date-time-zone'] = $this->lang->t('install', 'select_time_zone');
        }
        if (is_file($configFilePath) === false || is_writable($configFilePath) === false) {
            $errors[] = $this->lang->t('install', 'wrong_chmod_for_config_file');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

}