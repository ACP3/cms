<?php

namespace ACP3\Modules\System\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\System;

/**
 * Description of SystemAdmin
 *
 * @author Tino Goratsch
 */
class Maintenance extends Core\Modules\Controller\Admin
{
    /**
     *
     * @var System\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new System\Model($this->db);
    }

    public function actionIndex()
    {
        return;
    }

    public function actionSqlExport()
    {
        if (empty($_POST) === false) {
            try {
                $validator = new System\Validator($this->lang);
                $validator->validateSqlExport($_POST);

                $this->session->unsetFormToken();

                $export = System\Helpers::exportDatabase($_POST['tables'], $_POST['export_type'], isset($_POST['drop']) === true);

                // Als Datei ausgeben
                if ($_POST['output'] === 'file') {
                    header('Content-Type: text/sql');
                    header('Content-Disposition: attachment; filename=' . CONFIG_DB_NAME . '_export.sql');
                    header('Content-Length: ' . strlen($export));
                    exit($export);
                } else { // Im Browser ausgeben
                    $this->view->assign('export', htmlentities($export, ENT_QUOTES, 'UTF-8'));
                }

                return;
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/system/index/sql_import');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $dbTables = $this->model->getSchemaTables();
        $tables = array();
        foreach ($dbTables as $row) {
            $table = $row['TABLE_NAME'];
            if (strpos($table, CONFIG_DB_PRE) === 0) {
                $tables[$table]['name'] = $table;
                $tables[$table]['selected'] = Core\Functions::selectEntry('tables', $table);
            }
        }
        ksort($tables);
        $this->view->assign('tables', $tables);

        // Ausgabe
        $lang_output = array($this->lang->t('system', 'output_as_file'), $this->lang->t('system', 'output_as_text'));
        $this->view->assign('output', Core\Functions::selectGenerator('output', array('file', 'text'), $lang_output, 'file', 'checked'));

        // Exportart
        $lang_export_type = array(
            $this->lang->t('system', 'complete_export'),
            $this->lang->t('system', 'export_structure'),
            $this->lang->t('system', 'export_data')
        );
        $this->view->assign('export_type', Core\Functions::selectGenerator('export_type', array('complete', 'structure', 'data'), $lang_export_type, 'complete', 'checked'));

        $drop = array();
        $drop['checked'] = Core\Functions::selectEntry('drop', '1', '', 'checked');
        $drop['lang'] = $this->lang->t('system', 'drop_tables');
        $this->view->assign('drop', $drop);

        $this->session->generateFormToken();
    }

    public function actionSqlImport()
    {
        if (empty($_POST) === false) {
            try {
                $file = array();
                if (isset($_FILES['file'])) {
                    $file['tmp_name'] = $_FILES['file']['tmp_name'];
                    $file['name'] = $_FILES['file']['name'];
                    $file['size'] = $_FILES['file']['size'];
                }

                $validator = new System\Validator($this->lang);
                $validator->validateSqlImport($_POST, $file);

                $this->session->unsetFormToken();

                $data = isset($file) ? file_get_contents($file['tmp_name']) : $_POST['text'];
                $importData = explode(";\n", str_replace(array("\r\n", "\r", "\n"), "\n", $data));
                $sqlQueries = array();

                $i = 0;
                foreach ($importData as $row) {
                    if (!empty($row)) {
                        $bool = $this->db->query($row);
                        $sqlQueries[$i]['query'] = str_replace("\n", '<br />', $row);
                        $sqlQueries[$i]['color'] = $bool !== null ? '090' : 'f00';
                        ++$i;

                        if (!$bool) {
                            break;
                        }
                    }
                }

                $this->view->assign('sql_queries', $sqlQueries);

                Core\Cache::purge();
                return;
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/system/index/sql_import');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $this->view->assign('form', array_merge(array('text' => ''), $_POST));

        $this->session->generateFormToken();
    }

    public function actionUpdateCheck()
    {
        $file = @file_get_contents('http://www.acp3-cms.net/update.txt');
        if ($file !== false) {
            $data = explode('||', $file);
            if (count($data) === 2) {
                $update = array(
                    'installed_version' => CONFIG_VERSION,
                    'current_version' => $data[0],
                );

                if (version_compare($update['installed_version'], $update['current_version'], '>=')) {
                    $update['text'] = $this->lang->t('system', 'acp3_up_to_date');
                    $update['class'] = 'success';
                } else {
                    $update['text'] = sprintf($this->lang->t('system', 'acp3_not_up_to_date'), '<a href="' . $data[1] . '" onclick="window.open(this.href); return false">', '</a>');
                    $update['class'] = 'error';
                }

                $this->view->assign('update', $update);
            }
        }
    }

}