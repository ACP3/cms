<?php
/**
 * Created by PhpStorm.
 * User: goratsch
 * Date: 22.12.13
 * Time: 17:00
 */

namespace ACP3\Modules\System;


use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'modules';

    public function __construct(\Doctrine\DBAL\Connection $db, Core\Lang $lang)
    {
        parent::__construct($db, $lang);
    }

    public function getSchemaTables()
    {
        return $this->db->fetchAll('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_TYPE = ? AND TABLE_SCHEMA = ?', array('BASE TABLE', CONFIG_DB_NAME));
    }

    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::isInternalURI($formData['homepage']) === false)
            $errors['homepage'] = $this->lang->t('system', 'incorrect_homepage');
        if (Core\Validate::isNumber($formData['entries']) === false)
            $errors['entries'] = $this->lang->t('system', 'select_records_per_page');
        if (Core\Validate::isNumber($formData['flood']) === false)
            $errors['flood'] = $this->lang->t('system', 'type_in_flood_barrier');
        if ((bool)preg_match('/\/$/', $formData['icons_path']) === false)
            $errors['icons-path'] = $this->lang->t('system', 'incorrect_path_to_icons');
        if (preg_match('=/=', $formData['wysiwyg']) || is_file(CLASSES_DIR . 'WYSIWYG/' . $formData['wysiwyg'] . '.php') === false)
            $errors['wysiwyg'] = $this->lang->t('system', 'select_editor');
        if (empty($formData['date_format_long']) || empty($formData['date_format_short']))
            $errors[] = $this->lang->t('system', 'type_in_date_format');
        if (Core\Validate::timeZone($formData['date_time_zone']) === false)
            $errors['date-time-zone'] = $this->lang->t('system', 'select_time_zone');
        if (Core\Validate::isNumber($formData['maintenance_mode']) === false)
            $errors[] = $this->lang->t('system', 'select_online_maintenance');
        if (strlen($formData['maintenance_message']) < 3)
            $errors['maintenance-message'] = $this->lang->t('system', 'maintenance_message_to_short');
        if (empty($formData['seo_title']))
            $errors['seo-title'] = $this->lang->t('system', 'title_to_short');
        if (Core\Validate::isNumber($formData['seo_robots']) === false)
            $errors[] = $this->lang->t('system', 'select_seo_robots');
        if (Core\Validate::isNumber($formData['seo_aliases']) === false)
            $errors[] = $this->lang->t('system', 'select_seo_aliases');
        if (Core\Validate::isNumber($formData['seo_mod_rewrite']) === false)
            $errors[] = $this->lang->t('system', 'select_mod_rewrite');
        if (Core\Validate::isNumber($formData['cache_images']) === false)
            $errors[] = $this->lang->t('system', 'select_cache_images');
        if (Core\Validate::isNumber($formData['cache_minify']) === false)
            $errors['cache-minify'] = $this->lang->t('system', 'type_in_minify_cache_lifetime');
        if (!empty($formData['extra_css']) && Core\Validate::extraCSS($formData['extra_css']) === false)
            $errors['extra-css'] = $this->lang->t('system', 'type_in_additional_stylesheets');
        if (!empty($formData['extra_js']) && Core\Validate::extraJS($formData['extra_js']) === false)
            $errors['extra-js'] = $this->lang->t('system', 'type_in_additional_javascript_files');
        if ($formData['mailer_type'] === 'smtp') {
            if (empty($formData['mailer_smtp_host']))
                $errors['mailer-smtp-host'] = $this->lang->t('system', 'type_in_mailer_smtp_host');
            if (Core\Validate::isNumber($formData['mailer_smtp_port']) === false)
                $errors['mailer-smtp-port'] = $this->lang->t('system', 'type_in_mailer_smtp_port');
            if ($formData['mailer_smtp_auth'] == 1 && empty($formData['mailer_smtp_user']))
                $errors['mailer-smtp-username'] = $this->lang->t('system', 'type_in_mailer_smtp_username');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

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
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateSqlImport(array $formData, array $file)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['text']) && empty($file['size'])) {
            $errors['text'] = $this->lang->t('system', 'type_in_text_or_select_sql_file');
        }
        if (!empty($file['size']) &&
            (!Core\Validate::mimeType($file['tmp_name'], 'text/plain') ||
                $_FILES['file']['error'] !== UPLOAD_ERR_OK)
        ) {
            $errors['file'] = $this->lang->t('system', 'select_sql_file');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

}
