<?php

namespace ACP3\Modules\Files\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Categories;
use ACP3\Modules\Files;


/**
 * Description of FilesAdmin
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Admin
{

    /**
     * @var Files\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Files\Model($this->db);
    }

    public function actionCreate()
    {
        $config = new Core\Config($this->db, 'files');
        $settings = $config->getSettings();

        if (empty($_POST) === false) {
            try {
                if (isset($_POST['external'])) {
                    $file = $_POST['file_external'];
                } else {
                    $file = array();
                    $file['tmp_name'] = $_FILES['file_internal']['tmp_name'];
                    $file['name'] = $_FILES['file_internal']['name'];
                    $file['size'] = $_FILES['file_internal']['size'];
                }

                $validator = new Files\Validator($this->lang, $this->uri);
                $validator->validateCreate($_POST, $file);

                if (is_array($file) === true) {
                    $upload = new Core\Helpers\Upload('files');
                    $result = $upload->moveFile($file['tmp_name'], $file['name']);
                    $newFile = $result['name'];
                    $filesize = $result['size'];
                } else {
                    $_POST['filesize'] = (float)$_POST['filesize'];
                    $newFile = $file;
                    $filesize = $_POST['filesize'] . ' ' . $_POST['unit'];
                }

                $insertValues = array(
                    'id' => '',
                    'start' => $this->date->toSQL($_POST['start']),
                    'end' => $this->date->toSQL($_POST['end']),
                    'category_id' => strlen($_POST['cat_create']) >= 3 ? Categories\Helpers::categoriesCreate($_POST['cat_create'], 'files') : $_POST['cat'],
                    'file' => $newFile,
                    'size' => $filesize,
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'text' => Core\Functions::strEncode($_POST['text'], true),
                    'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
                    'user_id' => $this->auth->getUserId(),
                );


                $lastId = $this->model->insert($insertValues);

                $this->uri->insertUriAlias(
                    sprintf(Files\Helpers::URL_KEY_PATTERN, $lastId),
                    $_POST['alias'],
                    $_POST['seo_keywords'],
                    $_POST['seo_description'],
                    (int)$_POST['seo_robots']);
                $this->seo->setCache();

                $this->session->unsetFormToken();

                $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
                $redirect->setMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'acp/files');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
                $redirect->setMessage(false, $e->getMessage(), 'acp/files');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        // Datumsauswahl
        $this->view->assign('publication_period', $this->date->datepicker(array('start', 'end')));

        $units = array('Byte', 'KiB', 'MiB', 'GiB', 'TiB');
        $this->view->assign('units', Core\Functions::selectGenerator('units', $units, $units, ''));

        // Formularelemente
        $this->view->assign('categories', Categories\Helpers::categoriesList('files', '', true));

        if ($settings['comments'] == 1 && Core\Modules::isActive('comments') === true) {
            $options = array();
            $options[0]['name'] = 'comments';
            $options[0]['checked'] = Core\Functions::selectEntry('comments', '1', '0', 'checked');
            $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
            $this->view->assign('options', $options);
        }

        $this->view->assign('checked_external', isset($_POST['external']) ? ' checked="checked"' : '');

        $defaults = array(
            'title' => '',
            'file_internal' => '',
            'file_external' => '',
            'filesize' => '',
            'text' => '',
            'alias' => '',
            'seo_keywords' => '',
            'seo_description' => '',
        );

        $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields());

        $this->view->assign('form', array_merge($defaults, $_POST));

        $this->session->generateFormToken();
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/files/index/delete', 'acp/files');

        if ($this->uri->action === 'confirmed') {
            $bool = false;
            $commentsInstalled = Core\Modules::isInstalled('comments');

            $cache = new Core\Cache2('files');
            $upload = new Core\Helpers\Upload('files');
            foreach ($items as $item) {
                if (!empty($item)) {
                    $upload->removeUploadedFile($this->model->getFileById($item)); // Datei ebenfalls löschen
                    $bool = $this->model->delete($item);
                    if ($commentsInstalled === true) {
                        \ACP3\Modules\Comments\Helpers::deleteCommentsByModuleAndResult('files', $item);
                    }

                    $cache->delete(Files\Cache::CACHE_ID);
                    $this->uri->deleteUriAlias(sprintf(Files\Helpers::URL_KEY_PATTERN, $item));
                }
            }

            $this->seo->setCache();

            $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
            $redirect->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/files');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $dl = $this->model->getOneById((int)$this->uri->id);

        if (empty($dl) === false) {
            $config = new Core\Config($this->db, 'files');
            $settings = $config->getSettings();

            if (empty($_POST) === false) {
                try {
                    $file = array();
                    if (isset($_POST['external'])) {
                        $file = $_POST['file_external'];
                    } elseif (!empty($_FILES['file_internal']['name'])) {
                        $file = array();
                        $file['tmp_name'] = $_FILES['file_internal']['tmp_name'];
                        $file['name'] = $_FILES['file_internal']['name'];
                        $file['size'] = $_FILES['file_internal']['size'];
                    }

                    $validator = new Files\Validator($this->lang, $this->uri);
                    $validator->validateEdit($_POST, $file);

                    $updateValues = array(
                        'start' => $this->date->toSQL($_POST['start']),
                        'end' => $this->date->toSQL($_POST['end']),
                        'category_id' => strlen($_POST['cat_create']) >= 3 ? Categories\Helpers::categoriesCreate($_POST['cat_create'], 'files') : $_POST['cat'],
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'text' => Core\Functions::strEncode($_POST['text'], true),
                        'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
                        'user_id' => $this->auth->getUserId(),
                    );

                    // Falls eine neue Datei angegeben wurde, Änderungen durchführen
                    if (isset($file)) {
                        $upload = new Core\Helpers\Upload('files');

                        if (is_array($file) === true) {
                            $result = $upload->moveFile($file['tmp_name'], $file['name']);
                            $newFile = $result['name'];
                            $filesize = $result['size'];
                        } else {
                            $_POST['filesize'] = (float)$_POST['filesize'];
                            $newFile = $file;
                            $filesize = $_POST['filesize'] . ' ' . $_POST['unit'];
                        }
                        // SQL Query für die Änderungen
                        $newFileSql = array(
                            'file' => $newFile,
                            'size' => $filesize,
                        );

                        $upload->removeUploadedFile($dl['file']);

                        $updateValues = array_merge($updateValues, $newFileSql);
                    }

                    $bool = $this->model->update($updateValues, $this->uri->id);

                    $this->uri->insertUriAlias(
                        sprintf(Files\Helpers::URL_KEY_PATTERN, $this->uri->id),
                        $_POST['alias'],
                        $_POST['seo_keywords'],
                        $_POST['seo_description'],
                        (int)$_POST['seo_robots']
                    );
                    $this->seo->setCache();

                    $cache = new Files\Cache($this->model);
                    $cache->setCache($this->uri->id);

                    $this->session->unsetFormToken();

                    $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
                    $redirect->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/files');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
                    $redirect->setMessage(false, $e->getMessage(), 'acp/files');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                    $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
                }
            }

            // Datumsauswahl
            $this->view->assign('publication_period', $this->date->datepicker(array('start', 'end'), array($dl['start'], $dl['end'])));

            $units = array('Byte', 'KiB', 'MiB', 'GiB', 'TiB');
            $this->view->assign('units', Core\Functions::selectGenerator('units', $units, $units, trim(strrchr($dl['size'], ' '))));

            $dl['filesize'] = substr($dl['size'], 0, strpos($dl['size'], ' '));

            // Formularelemente
            $this->view->assign('categories', Categories\Helpers::categoriesList('files', $dl['category_id'], true));

            if ($settings['comments'] == 1 && Core\Modules::isActive('comments') === true) {
                $options = array();
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = Core\Functions::selectEntry('comments', '1', $dl['comments'], 'checked');
                $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            $this->view->assign('checked_external', isset($_POST['external']) ? ' checked="checked"' : '');
            $this->view->assign('current_file', $dl['file']);

            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields(sprintf(Files\Helpers::URL_KEY_PATTERN, $this->uri->id)));
            $this->view->assign('form', array_merge($dl, $_POST));

            $this->session->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
        $redirect->getMessage();

        $files = $this->model->getAllInAcp();
        $c_files = count($files);

        if ($c_files > 0) {
            $canDelete = Core\Modules::hasPermission('admin/files/index/delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->appendContent(Core\Functions::dataTable($config));
            for ($i = 0; $i < $c_files; ++$i) {
                $files[$i]['period'] = $this->date->formatTimeRange($files[$i]['start'], $files[$i]['end']);
                $files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : $this->lang->t('files', 'unknown_filesize');
            }
            $this->view->assign('files', $files);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    public function actionSettings()
    {
        $config = new Core\Config($this->db, 'files');

        if (empty($_POST) === false) {
            try {
                $validator = new Files\Validator($this->lang, $this->uri);
                $validator->validateSettings($_POST);

                $data = array(
                    'dateformat' => Core\Functions::strEncode($_POST['dateformat']),
                    'sidebar' => (int)$_POST['sidebar'],
                    'comments' => $_POST['comments']
                );
                $bool = $config->setSettings($data);

                $this->session->unsetFormToken();

                $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
                $redirect->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/files');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
                $redirect->setMessage(false, $e->getMessage(), 'acp/files');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        $settings = $config->getSettings();

        if (Core\Modules::isActive('comments') === true) {
            $lang_comments = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('comments', Core\Functions::selectGenerator('comments', array(1, 0), $lang_comments, $settings['comments'], 'checked'));
        }

        $this->view->assign('dateformat', $this->date->dateFormatDropdown($settings['dateformat']));

        $this->view->assign('sidebar_entries', Core\Functions::recordsPerPage((int)$settings['sidebar'], 1, 10));

        $this->session->generateFormToken();
    }

}