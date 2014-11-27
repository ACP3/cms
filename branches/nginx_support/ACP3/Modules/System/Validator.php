<?php
namespace ACP3\Modules\System;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\System
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Validator\Rules\Date
     */
    protected $dateValidator;
    /**
     * @var Core\Validator\Rules\Mime
     */
    protected $mimeValidator;
    /**
     * @var Core\Validator\Rules\Router
     */
    protected $routerValidator;

    /**
     * @param Core\Lang $lang
     * @param Core\Validator\Rules\Misc $validate
     * @param Core\Validator\Rules\Date $dateValidator
     * @param Core\Validator\Rules\Mime $mimeValidator
     * @param Core\Validator\Rules\Router $routerValidator
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Date $dateValidator,
        Core\Validator\Rules\Mime $mimeValidator,
        Core\Validator\Rules\Router $routerValidator
    ) {
        parent::__construct($lang, $validate);

        $this->dateValidator = $dateValidator;
        $this->mimeValidator = $mimeValidator;
        $this->routerValidator = $routerValidator;
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
        if ($this->routerValidator->isInternalURI($formData['homepage']) === false) {
            $this->errors['homepage'] = $this->lang->t('system', 'incorrect_homepage');
        }
        if ($this->validate->isNumber($formData['entries']) === false) {
            $this->errors['entries'] = $this->lang->t('system', 'select_records_per_page');
        }
        if ($this->validate->isNumber($formData['flood']) === false) {
            $this->errors['flood'] = $this->lang->t('system', 'type_in_flood_barrier');
        }
        if (preg_match('=/=', $formData['wysiwyg']) || is_file(CLASSES_DIR . 'WYSIWYG/' . $formData['wysiwyg'] . '.php') === false) {
            $this->errors['wysiwyg'] = $this->lang->t('system', 'select_editor');
        }
        if ($this->lang->languagePackExists($formData['language']) === false) {
            $this->errors['language'] = $this->lang->t('system', 'select_language');
        }
        if (empty($formData['date_format_long'])) {
            $this->errors['date-format-long'] = $this->lang->t('system', 'type_in_long_date_format');
        }
        if (empty($formData['date_format_short'])) {
            $this->errors['date-format-short'] = $this->lang->t('system', 'type_in_short_date_format');
        }
        if ($this->dateValidator->timeZone($formData['date_time_zone']) === false) {
            $this->errors['date-time-zone'] = $this->lang->t('system', 'select_time_zone');
        }
        if ($this->validate->isNumber($formData['maintenance_mode']) === false) {
            $this->errors['maintenance-mode'] = $this->lang->t('system', 'select_online_maintenance');
        }
        if (strlen($formData['maintenance_message']) < 3) {
            $this->errors['maintenance-message'] = $this->lang->t('system', 'maintenance_message_to_short');
        }
        if (empty($formData['seo_title'])) {
            $this->errors['seo-title'] = $this->lang->t('system', 'title_to_short');
        }
        if ($this->validate->isNumber($formData['seo_robots']) === false) {
            $this->errors['seo-robots'] = $this->lang->t('system', 'select_seo_robots');
        }
        if ($this->validate->isNumber($formData['seo_mod_rewrite']) === false) {
            $this->errors['seo-mod-rewrite'] = $this->lang->t('system', 'select_mod_rewrite');
        }
        if ($this->validate->isNumber($formData['cache_images']) === false) {
            $this->errors['cache-images'] = $this->lang->t('system', 'select_cache_images');
        }
        if ($this->validate->isNumber($formData['cache_minify']) === false) {
            $this->errors['cache-minify'] = $this->lang->t('system', 'type_in_minify_cache_lifetime');
        }
        if ($formData['mailer_type'] === 'smtp') {
            if (empty($formData['mailer_smtp_host'])) {
                $this->errors['mailer-smtp-host'] = $this->lang->t('system', 'type_in_mailer_smtp_host');
            }
            if ($this->validate->isNumber($formData['mailer_smtp_port']) === false) {
                $this->errors['mailer-smtp-port'] = $this->lang->t('system', 'type_in_mailer_smtp_port');
            }
            if ($formData['mailer_smtp_auth'] == 1 && empty($formData['mailer_smtp_user'])) {
                $this->errors['mailer-smtp-username'] = $this->lang->t('system', 'type_in_mailer_smtp_username');
            }
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSqlExport(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['tables']) || is_array($formData['tables']) === false) {
            $this->errors['tables'] = $this->lang->t('system', 'select_sql_tables');
        }
        if ($formData['output'] !== 'file' && $formData['output'] !== 'text') {
            $this->errors['output'] = $this->lang->t('system', 'select_output');
        }
        if (in_array($formData['export_type'], ['complete', 'structure', 'data']) === false) {
            $this->errors['export-type'] = $this->lang->t('system', 'select_export_type');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     * @param array $file
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSqlImport(array $formData, array $file)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['text']) && empty($file['size'])) {
            $this->errors['text'] = $this->lang->t('system', 'type_in_text_or_select_sql_file');
        }
        if (!empty($file['size']) &&
            (!$this->mimeValidator->mimeType($file['tmp_name'], 'text/plain') ||
                $_FILES['file']['error'] !== UPLOAD_ERR_OK)
        ) {
            $this->errors['file'] = $this->lang->t('system', 'select_sql_file');
        }

        $this->_checkForFailedValidation();
    }
}
