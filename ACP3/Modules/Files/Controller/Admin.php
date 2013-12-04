<?php

namespace ACP3\Modules\Files\Controller;

use ACP3\Core;
use ACP3\Modules\Categories;
use ACP3\Modules\Files;


/**
 * Description of FilesAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller\Admin
{

    /**
     *
     * @var Model
     */
    protected $model;

    public function __construct()
    {
        parent::__construct();

        $this->model = new Files\Model($this->db);
    }

    public function actionCreate()
    {
        $settings = Core\Config::getSettings('files');

        if (isset($_POST['submit']) === true) {
            try {
                if (isset($_POST['external'])) {
                    $file = $_POST['file_external'];
                } else {
                    $file = array();
                    $file['tmp_name'] = $_FILES['file_internal']['tmp_name'];
                    $file['name'] = $_FILES['file_internal']['name'];
                    $file['size'] = $_FILES['file_internal']['size'];
                }

                $this->model->validateCreate($_POST, $file, $this->lang);

                if (is_array($file) === true) {
                    $result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'files');
                    $new_file = $result['name'];
                    $filesize = $result['size'];
                } else {
                    $_POST['filesize'] = (float)$_POST['filesize'];
                    $new_file = $file;
                    $filesize = $_POST['filesize'] . ' ' . $_POST['unit'];
                }

                $insert_values = array(
                    'id' => '',
                    'start' => $this->date->toSQL($_POST['start']),
                    'end' => $this->date->toSQL($_POST['end']),
                    'category_id' => strlen($_POST['cat_create']) >= 3 ? Categories\Helpers::categoriesCreate($_POST['cat_create'], 'files') : $_POST['cat'],
                    'file' => $new_file,
                    'size' => $filesize,
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'text' => Core\Functions::strEncode($_POST['text'], true),
                    'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
                    'user_id' => $this->auth->getUserId(),
                );


                $lastId = $this->model->insert($insert_values);
                if ((bool)CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
                    Core\SEO::insertUriAlias('files/details/id_' . $lastId, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int)$_POST['seo_robots']);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'acp/files');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/files');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
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

        $this->view->assign('SEO_FORM_FIELDS', Core\SEO::formFields());

        $this->view->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

        $this->session->generateFormToken();
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/files/delete', 'acp/files');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = false;
            $commentsInstalled = Core\Modules::isInstalled('comments');
            foreach ($items as $item) {
                if (!empty($item)) {
                    Core\Functions::removeUploadedFile('files', $this->model->getFileById($item)); // Datei ebenfalls löschen
                    $bool = $this->model->delete($item);
                    if ($commentsInstalled === true) {
                        \ACP3\Modules\Comments\Helpers::deleteCommentsByModuleAndResult('files', $item);
                    }

                    Core\Cache::delete('details_id_' . $item, 'files');
                    Core\SEO::deleteUriAlias('files/details/id_' . $item);
                }
            }
            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/files');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEdit()
    {
        $dl = $this->model->getOneById((int)$this->uri->id);

        if (empty($dl) === false) {
            $settings = Core\Config::getSettings('files');

            if (isset($_POST['submit']) === true) {
                try {
                    if (isset($_POST['external'])) {
                        $file = $_POST['file_external'];
                    } elseif (!empty($_FILES['file_internal']['name'])) {
                        $file = array();
                        $file['tmp_name'] = $_FILES['file_internal']['tmp_name'];
                        $file['name'] = $_FILES['file_internal']['name'];
                        $file['size'] = $_FILES['file_internal']['size'];
                    }

                    $this->model->validateEdit($_POST, $file, $this->lang);

                    $update_values = array(
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
                        if (is_array($file) === true) {
                            $result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'files');
                            $new_file = $result['name'];
                            $filesize = $result['size'];
                        } else {
                            $_POST['filesize'] = (float)$_POST['filesize'];
                            $new_file = $file;
                            $filesize = $_POST['filesize'] . ' ' . $_POST['unit'];
                        }
                        // SQL Query für die Änderungen
                        $new_file_sql = array(
                            'file' => $new_file,
                            'size' => $filesize,
                        );

                        Core\Functions::removeUploadedFile('files', $dl['file']);

                        $update_values = array_merge($update_values, $new_file_sql);
                    }

                    $bool = $this->model->update($update_values, $this->uri->id);
                    if ((bool)CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
                        Core\SEO::insertUriAlias('files/details/id_' . $this->uri->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int)$_POST['seo_robots']);

                    $this->model->setFilesCache($this->uri->id);

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/files');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/files');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
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

            $this->view->assign('SEO_FORM_FIELDS', Core\SEO::formFields('files/details/id_' . $this->uri->id));
            $this->view->assign('form', isset($_POST['submit']) ? $_POST : $dl);

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/403');
        }
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $files = $this->model->getAllInAcp();
        $c_files = count($files);

        if ($c_files > 0) {
            $can_delete = Core\Modules::hasPermission('files', 'acp_delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $can_delete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $can_delete === true ? 0 : ''
            );
            $this->view->appendContent(Core\Functions::datatable($config));
            for ($i = 0; $i < $c_files; ++$i) {
                $files[$i]['period'] = $this->date->formatTimeRange($files[$i]['start'], $files[$i]['end']);
                $files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : $this->lang->t('files', 'unknown_filesize');
            }
            $this->view->assign('files', $files);
            $this->view->assign('can_delete', $can_delete);
        }
    }

    public function actionSettings()
    {
        if (isset($_POST['submit']) === true) {
            try {
                $this->model->validateSettings($_POST, $this->lang);

                $data = array(
                    'dateformat' => Core\Functions::strEncode($_POST['dateformat']),
                    'sidebar' => (int)$_POST['sidebar'],
                    'comments' => $_POST['comments']
                );
                $bool = Core\Config::setSettings('files', $data);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/files');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/files');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $settings = Core\Config::getSettings('files');

        if (Core\Modules::isActive('comments') === true) {
            $lang_comments = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('comments', Core\Functions::selectGenerator('comments', array(1, 0), $lang_comments, $settings['comments'], 'checked'));
        }

        $this->view->assign('dateformat', $this->date->dateformatDropdown($settings['dateformat']));

        $this->view->assign('sidebar_entries', Core\Functions::recordsPerPage((int)$settings['sidebar'], 1, 10));

        $this->session->generateFormToken();
    }

}
