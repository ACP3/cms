<?php

namespace ACP3\Modules\Emoticons\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Emoticons;

/**
 * Description of EmoticonsAdmin
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Admin
{

    /**
     *
     * @var Emoticons\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Emoticons\Model($this->db);
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            try {
                $file = array();
                if (!empty($_FILES['picture']['tmp_name'])) {
                    $file['tmp_name'] = $_FILES['picture']['tmp_name'];
                    $file['name'] = $_FILES['picture']['name'];
                    $file['size'] = $_FILES['picture']['size'];
                }

                $config = new Core\Config($this->db, 'emoticons');

                $validator = new Emoticons\Validator($this->lang);
                $validator->validateCreate($_POST, $file, $config->getSettings());

                $result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'emoticons');

                $insertValues = array(
                    'id' => '',
                    'code' => Core\Functions::strEncode($_POST['code']),
                    'description' => Core\Functions::strEncode($_POST['description']),
                    'img' => $result['name'],
                );

                $bool = $this->model->insert($insertValues);
                $this->model->setCache();

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/emoticons');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/categories');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $this->view->assign('form', array_merge(array('code' => '', 'description' => ''), $_POST));

        $this->session->generateFormToken();
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/emoticons/index/delete', 'acp/emoticons');

        if ($this->uri->action === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                if (!empty($item) && $this->model->resultExists($item) === true) {
                    // Datei ebenfalls löschen
                    $file = $this->model->getOneImageById($item);
                    Core\Functions::removeUploadedFile('emoticons', $file);
                    $bool = $this->model->delete($item);
                }
            }

            $this->model->setCache();

            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/emoticons');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $emoticon = $this->model->getOneById((int)$this->uri->id);

        if (empty($emoticon) === false) {
            if (empty($_POST) === false) {
                try {
                    $file = array();
                    if (!empty($_FILES['picture']['name'])) {
                        $file['tmp_name'] = $_FILES['picture']['tmp_name'];
                        $file['name'] = $_FILES['picture']['name'];
                        $file['size'] = $_FILES['picture']['size'];
                    }

                    $config = new Core\Config($this->db, 'emoticons');

                    $validator = new Emoticons\Validator($this->lang);
                    $validator->validateEdit($_POST, $file, $config->getSettings());

                    $updateValues = array(
                        'code' => Core\Functions::strEncode($_POST['code']),
                        'description' => Core\Functions::strEncode($_POST['description']),
                    );

                    if (empty($file) === false) {
                        Core\Functions::removeUploadedFile('emoticons', $emoticon['img']);
                        $result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'emoticons');
                        $updateValues['img'] = $result['name'];
                    }

                    $bool = $this->model->update($updateValues, $this->uri->id);
                    $this->model->setCache();

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/emoticons');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/news');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            $this->view->assign('form', array_merge($emoticon, $_POST));

            $this->session->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        Core\Functions::getRedirectMessage();

        $emoticons = $this->model->getAll();
        $c_emoticons = count($emoticons);

        if ($c_emoticons > 0) {
            $canDelete = Core\Modules::hasPermission('admin/emoticons/index/delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 4 : 3,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->view->assign('emoticons', $emoticons);
            $this->view->assign('can_delete', $canDelete);
            $this->appendContent(Core\Functions::dataTable($config));
        }
    }

    public function actionSettings()
    {
        $config = new Core\Config($this->db, 'emoticons');

        if (empty($_POST) === false) {
            try {
                $validator = new Emoticons\Validator($this->lang);
                $validator->validateSettings($_POST);

                $data = array(
                    'width' => (int)$_POST['width'],
                    'height' => (int)$_POST['height'],
                    'filesize' => (int)$_POST['filesize'],
                );
                $bool = $config->setSettings($data);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/emoticons');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/emoticons');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $this->view->assign('form', array_merge($config->getSettings(), $_POST));

        $this->session->generateFormToken();
    }

}
