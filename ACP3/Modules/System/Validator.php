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

    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Date $dateValidator,
        Core\Validator\Rules\Mime $mimeValidator,
        Core\Validator\Rules\Router $routerValidator
    )
    {
        parent::__construct($lang, $validate);

        $this->dateValidator = $dateValidator;
        $this->mimeValidator = $mimeValidator;
        $this->routerValidator = $routerValidator;
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if ($this->routerValidator->isInternalURI($formData['homepage']) === false) {
            $errors['homepage'] = $this->lang->t('system', 'incorrect_homepage');
        }
        if ($this->validate->isNumber($formData['entries']) === false) {
            $errors['entries'] = $this->lang->t('system', 'select_records_per_page');
        }
        if ($this->validate->isNumber($formData['flood']) === false) {
            $errors['flood'] = $this->lang->t('system', 'type_in_flood_barrier');
        }
        if ((bool)preg_match('/\/$/', $formData['icons_path']) === false) {
            $errors['icons-path'] = $this->lang->t('system', 'incorrect_path_to_icons');
        }
        if (preg_match('=/=', $formData['wysiwyg']) || is_file(CLASSES_DIR . 'WYSIWYG/' . $formData['wysiwyg'] . '.php') === false) {
            $errors['wysiwyg'] = $this->lang->t('system', 'select_editor');
        }
        if ($this->lang->languagePackExists($formData['language']) === false) {
            $errors['language'] = $this->lang->t('system', 'select_language');
        }
        if (empty($formData['date_format_long']) || empty($formData['date_format_short'])) {
            $errors[] = $this->lang->t('system', 'type_in_date_format');
        }
        if ($this->dateValidator->timeZone($formData['date_time_zone']) === false) {
            $errors['date-time-zone'] = $this->lang->t('system', 'select_time_zone');
        }
        if ($this->validate->isNumber($formData['maintenance_mode']) === false) {
            $errors[] = $this->lang->t('system', 'select_online_maintenance');
        }
        if (strlen($formData['maintenance_message']) < 3) {
            $errors['maintenance-message'] = $this->lang->t('system', 'maintenance_message_to_short');
        }
        if (empty($formData['seo_title'])) {
            $errors['seo-title'] = $this->lang->t('system', 'title_to_short');
        }
        if ($this->validate->isNumber($formData['seo_robots']) === false) {
            $errors[] = $this->lang->t('system', 'select_seo_robots');
        }
        if ($this->validate->isNumber($formData['seo_mod_rewrite']) === false) {
            $errors[] = $this->lang->t('system', 'select_mod_rewrite');
        }
        if ($this->validate->isNumber($formData['cache_images']) === false) {
            $errors[] = $this->lang->t('system', 'select_cache_images');
        }
        if ($this->validate->isNumber($formData['cache_minify']) === false) {
            $errors['cache-minify'] = $this->lang->t('system', 'type_in_minify_cache_lifetime');
        }
        if (!empty($formData['extra_css']) && $this->_extraCSS($formData['extra_css']) === false) {
            $errors['extra-css'] = $this->lang->t('system', 'type_in_additional_stylesheets');
        }
        if (!empty($formData['extra_js']) && $this->_extraJS($formData['extra_js']) === false) {
            $errors['extra-js'] = $this->lang->t('system', 'type_in_additional_javascript_files');
        }
        if ($formData['mailer_type'] === 'smtp') {
            if (empty($formData['mailer_smtp_host'])) {
                $errors['mailer-smtp-host'] = $this->lang->t('system', 'type_in_mailer_smtp_host');
            }
            if ($this->validate->isNumber($formData['mailer_smtp_port']) === false) {
                $errors['mailer-smtp-port'] = $this->lang->t('system', 'type_in_mailer_smtp_port');
            }
            if ($formData['mailer_smtp_auth'] == 1 && empty($formData['mailer_smtp_user'])) {
                $errors['mailer-smtp-username'] = $this->lang->t('system', 'type_in_mailer_smtp_username');
            }
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateSqlExport(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['tables']) || is_array($formData['tables']) === false) {
            $errors['tables'] = $this->lang->t('system', 'select_sql_tables');
        }
        if ($formData['output'] !== 'file' && $formData['output'] !== 'text') {
            $errors[] = $this->lang->t('system', 'select_output');
        }
        if (in_array($formData['export_type'], array('complete', 'structure', 'data')) === false) {
            $errors[] = $this->lang->t('system', 'select_export_type');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @param array $file
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateSqlImport(array $formData, array $file)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['text']) && empty($file['size'])) {
            $errors['text'] = $this->lang->t('system', 'type_in_text_or_select_sql_file');
        }
        if (!empty($file['size']) &&
            (!$this->mimeValidator->mimeType($file['tmp_name'], 'text/plain') ||
                $_FILES['file']['error'] !== UPLOAD_ERR_OK)
        ) {
            $errors['file'] = $this->lang->t('system', 'select_sql_file');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * Überprüft, ob die zusätzlich zu ladenden Stylesheets überhaupt existieren
     *
     * @param string $var
     * @return boolean
     */
    private function _extraCSS($var)
    {
        if ((bool)preg_match('=/=', $var) === false) {
            $var_ary = explode(',', $var);
            foreach ($var_ary as $stylesheet) {
                $stylesheet = trim($stylesheet);
                if (is_file(DESIGN_PATH_INTERNAL . 'css/' . $stylesheet) === false) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Überprüft, ob die zusätzlich zu ladenden JavaScript Dateien überhaupt existieren
     *
     * @param string $var
     * @return boolean
     */
    private function _extraJS($var)
    {
        if ((bool)preg_match('=/=', $var) === false) {
            $var_ary = explode(',', $var);
            foreach ($var_ary as $js) {
                $js = trim($js);
                if (is_file(DESIGN_PATH_INTERNAL . 'js/' . $js) === false) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

} 