<?php

namespace ACP3\Modules\ACP3\System\Controller\Admin;

use ACP3\Application;
use ACP3\Core;
use ACP3\Modules\ACP3\System;

/**
 * Class Maintenance
 * @package ACP3\Modules\ACP3\System\Controller\Admin
 */
class Maintenance extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\DB
     */
    protected $db;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\System\Helpers
     */
    protected $systemHelpers;
    /**
     * @var \ACP3\Modules\ACP3\System\Model
     */
    protected $systemModel;
    /**
     * @var \ACP3\Modules\ACP3\System\Validator
     */
    protected $systemValidator;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\DB                              $db
     * @param \ACP3\Core\Helpers\FormToken               $formTokenHelper
     * @param \ACP3\Modules\ACP3\System\Helpers          $systemHelpers
     * @param \ACP3\Modules\ACP3\System\Model            $systemModel
     * @param \ACP3\Modules\ACP3\System\Validator        $systemValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\DB $db,
        Core\Helpers\FormToken $formTokenHelper,
        System\Helpers $systemHelpers,
        System\Model $systemModel,
        System\Validator $systemValidator)
    {
        parent::__construct($context);

        $this->db = $db;
        $this->formTokenHelper = $formTokenHelper;
        $this->systemHelpers = $systemHelpers;
        $this->systemModel = $systemModel;
        $this->systemValidator = $systemValidator;
    }

    /**
     * @param string $action
     */
    public function actionCache($action = '')
    {
        if (!empty($action)) {
            $result = false;
            switch ($action) {
                case 'general':
                    $result = Core\Cache::purge(CACHE_DIR . 'sql');
                    $text = $this->lang->t('system', $result === true ? 'cache_type_general_delete_success' : 'cache_type_general_delete_success');
                    break;
                case 'images':
                    $result = Core\Cache::purge(CACHE_DIR . 'images');
                    $text = $this->lang->t('system', $result === true ? 'cache_type_images_delete_success' : 'cache_type_images_delete_success');
                    break;
                case 'minify':
                    $result = Core\Cache::purge(UPLOADS_DIR . 'assets');
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
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_sqlExportPost($this->request->getPost()->getAll());
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

        $this->formTokenHelper->generateFormToken();
    }

    public function actionSqlImport()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_sqlImportPost($this->request->getPost()->getAll());
        }

        $this->view->assign('form', array_merge(['text' => ''], $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken();
    }

    public function actionUpdateCheck()
    {
        $file = @file_get_contents('http://www.acp3-cms.net/update.txt');
        if ($file !== false) {
            $data = explode('||', $file);
            if (count($data) === 2) {
                $update = [
                    'installed_version' => Core\ApplicationInterface::VERSION,
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
    protected function _sqlExportPost(array $formData)
    {
        $this->handlePostAction(
            function () use ($formData) {
                $this->systemValidator->validateSqlExport($formData);

                $this->formTokenHelper->unsetFormToken();

                $export = $this->systemHelpers->exportDatabase($formData['tables'], $formData['export_type'], isset($formData['drop']) === true);

                // Als Datei ausgeben
                if ($formData['output'] === 'file') {
                    header('Content-Type: text/sql');
                    header('Content-Disposition: attachment; filename=' . $this->db->getDatabase() . '_export.sql');
                    header('Content-Length: ' . strlen($export));
                    exit($export);
                } else { // Im Browser ausgeben
                    $this->view->assign('export', htmlentities($export, ENT_QUOTES, 'UTF-8'));
                }
            },
            $this->request->getFullPath()
        );
    }

    /**
     * @param array $formData
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function _sqlImportPost(array $formData)
    {
        $this->handlePostAction(
            function () use ($formData) {
                $file = $this->request->getFiles()->get('file');

                $this->systemValidator->validateSqlImport($formData, $file);

                $this->formTokenHelper->unsetFormToken();

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
                Core\Cache::purge(CACHE_DIR . 'sql');
                Core\Cache::purge(CACHE_DIR . 'tpl_compiled');
                Core\Cache::purge(CACHE_DIR . 'tpl_cached');
                Core\Cache::purge(UPLOADS_DIR . 'assets');
            },
            $this->request->getFullPath()
        );
    }
}
