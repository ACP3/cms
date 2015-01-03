<?php

namespace ACP3\Modules\System\Controller\Admin;

use ACP3\Application;
use ACP3\Core;
use ACP3\Modules\System;

/**
 * Class Maintenance
 * @package ACP3\Modules\System\Controller\Admin
 */
class Maintenance extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\DB
     */
    protected $db;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\System\Helpers
     */
    protected $systemHelpers;
    /**
     * @var \ACP3\Modules\System\Model
     */
    protected $systemModel;
    /**
     * @var \ACP3\Modules\System\Validator
     */
    protected $systemValidator;

    /**
     * @param \ACP3\Core\Context\Admin       $context
     * @param \ACP3\Core\DB                  $db
     * @param \ACP3\Core\Helpers\Secure      $secureHelper
     * @param \ACP3\Modules\System\Helpers   $systemHelpers
     * @param \ACP3\Modules\System\Model     $systemModel
     * @param \ACP3\Modules\System\Validator $systemValidator
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\DB $db,
        Core\Helpers\Secure $secureHelper,
        System\Helpers $systemHelpers,
        System\Model $systemModel,
        System\Validator $systemValidator)
    {
        parent::__construct($context);

        $this->db = $db;
        $this->secureHelper = $secureHelper;
        $this->systemHelpers = $systemHelpers;
        $this->systemModel = $systemModel;
        $this->systemValidator = $systemValidator;
    }

    public function actionCache()
    {
        if (isset($this->request->action)) {
            $result = false;
            switch ($this->request->action) {
                case 'general':
                    $result = Core\Cache::purge(CACHE_DIR . 'sql');
                    $text = $this->lang->t('system', $result === true ? 'cache_type_general_delete_success' : 'cache_type_general_delete_success');
                    break;
                case 'images':
                    $result = Core\Cache::purge(CACHE_DIR . 'images');
                    $text = $this->lang->t('system', $result === true ? 'cache_type_images_delete_success' : 'cache_type_images_delete_success');
                    break;
                case 'minify':
                    $result = Core\Cache::purge(CACHE_DIR . 'minify');
                    $text = $this->lang->t('system', $result === true ? 'cache_type_minify_delete_success' : 'cache_type_minify_delete_success');
                    break;
                case 'templates':
                    $result = (Core\Cache::purge(CACHE_DIR . 'tpl_compiled') && Core\Cache::purge(CACHE_DIR . 'tpl_cached'));
                    $text = $this->lang->t('system', $result === true ? 'cache_type_templates_delete_success' : 'cache_type_templates_delete_success');
                    break;
                default:
                    $text = $this->lang->t('system', 'cache_type_not_found');
            }

            $this->redirectMessages()->setMessage($result, $text, 'acp/system/maintenance/cache');
        }

        return;
    }

    public function actionIndex()
    {
        return;
    }

    public function actionSqlExport()
    {
        if (empty($_POST) === false) {
            $this->_sqlExportPost($_POST);
        }

        $dbTables = $this->systemModel->getSchemaTables();
        $tables = [];
        foreach ($dbTables as $row) {
            $table = $row['TABLE_NAME'];
            if (strpos($table, $this->db->getPrefix()) === 0) {
                $tables[$table]['name'] = $table;
                $tables[$table]['selected'] = $this->get('core.helpers.forms')->selectEntry('tables', $table);
            }
        }
        ksort($tables);
        $this->view->assign('tables', $tables);

        // Ausgabe
        $lang_output = [$this->lang->t('system', 'output_as_file'), $this->lang->t('system', 'output_as_text')];
        $this->view->assign('output', $this->get('core.helpers.forms')->selectGenerator('output', ['file', 'text'], $lang_output, 'file', 'checked'));

        // Exportart
        $lang_exportType = [
            $this->lang->t('system', 'complete_export'),
            $this->lang->t('system', 'export_structure'),
            $this->lang->t('system', 'export_data')
        ];
        $this->view->assign('export_type', $this->get('core.helpers.forms')->selectGenerator('export_type', ['complete', 'structure', 'data'], $lang_exportType, 'complete', 'checked'));

        $drop = [];
        $drop['checked'] = $this->get('core.helpers.forms')->selectEntry('drop', '1', '', 'checked');
        $drop['lang'] = $this->lang->t('system', 'drop_tables');
        $this->view->assign('drop', $drop);

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionSqlImport()
    {
        if (empty($_POST) === false) {
            $this->_sqlImportPost($_POST);
        }

        $this->view->assign('form', array_merge(['text' => ''], $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionUpdateCheck()
    {
        $file = @file_get_contents('http://www.acp3-cms.net/update.txt');
        if ($file !== false) {
            $data = explode('||', $file);
            if (count($data) === 2) {
                $update = [
                    'installed_version' => Application::VERSION,
                    'current_version' => $data[0],
                ];

                if (version_compare($update['installed_version'], $update['current_version'], '>=')) {
                    $update['text'] = $this->lang->t('system', 'acp3_up_to_date');
                    $update['class'] = 'success';
                } else {
                    $update['text'] = sprintf($this->lang->t('system', 'acp3_not_up_to_date'), '<a href="' . $data[1] . '" target="_blank">', '</a>');
                    $update['class'] = 'error';
                }

                $this->view->assign('update', $update);
            }
        }
    }

    /**
     * @param array $formData
     */
    private function _sqlExportPost(array $formData)
    {
        try {
            $this->systemValidator->validateSqlExport($formData);

            $this->secureHelper->unsetFormToken($this->request->query);

            $export = $this->systemHelpers->exportDatabase($formData['tables'], $formData['export_type'], isset($formData['drop']) === true);

            // Als Datei ausgeben
            if ($formData['output'] === 'file') {
                header('Content-Type: text/sql');
                header('Content-Disposition: attachment; filename=' . $this->db->getName() . '_export.sql');
                header('Content-Length: ' . strlen($export));
                exit($export);
            } else { // Im Browser ausgeben
                $this->view->assign('export', htmlentities($export, ENT_QUOTES, 'UTF-8'));
            }

            return;
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/system/maintenance/sql_export');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function _sqlImportPost(array $formData)
    {
        try {
            $file = [];
            if (isset($_FILES['file'])) {
                $file['tmp_name'] = $_FILES['file']['tmp_name'];
                $file['name'] = $_FILES['file']['name'];
                $file['size'] = $_FILES['file']['size'];
            }

            $this->systemValidator->validateSqlImport($formData, $file);

            $this->secureHelper->unsetFormToken($this->request->query);

            $data = isset($file) ? file_get_contents($file['tmp_name']) : $formData['text'];
            $importData = explode(";\n", str_replace(["\r\n", "\r", "\n"], "\n", $data));
            $sqlQueries = [];

            $i = 0;
            foreach ($importData as $row) {
                if (!empty($row)) {
                    $bool = $this->db->getConnection()->query($row);
                    $sqlQueries[$i]['query'] = str_replace("\n", '<br />', $row);
                    $sqlQueries[$i]['color'] = $bool !== null ? '090' : 'f00';
                    ++$i;

                    if (!$bool) {
                        break;
                    }
                }
            }

            $this->view->assign('sql_queries', $sqlQueries);

            Core\Cache::purge(CACHE_DIR . 'images');
            Core\Cache::purge(CACHE_DIR . 'minify');
            Core\Cache::purge(CACHE_DIR . 'sql');
            Core\Cache::purge(CACHE_DIR . 'tpl_compiled');
            Core\Cache::purge(CACHE_DIR . 'tpl_cached');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/system/maintenance/sql_import');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
