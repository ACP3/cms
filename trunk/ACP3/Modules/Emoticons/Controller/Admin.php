<?php

namespace ACP3\Modules\Emoticons\Controller;

use ACP3\Core;
use ACP3\Modules\Emoticons;

/**
 * Description of EmoticonsAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller\Admin
{

    /**
     *
     * @var Model
     */
    private $model;

    public function __construct()
    {
        parent::__construct();

        $this->model = new Emoticons\Model($this->db);
    }

    public function actionCreate()
    {
        if (isset($_POST['submit']) === true) {
            try {
                $file = array();
                if (!empty($_FILES['picture']['tmp_name'])) {
                    $file['tmp_name'] = $_FILES['picture']['tmp_name'];
                    $file['name'] = $_FILES['picture']['name'];
                    $file['size'] = $_FILES['picture']['size'];
                }

                $this->model->validateCreate($_POST, $file, $this->lang);

                $result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'emoticons');

                $insert_values = array(
                    'id' => '',
                    'code' => Core\Functions::strEncode($_POST['code']),
                    'description' => Core\Functions::strEncode($_POST['description']),
                    'img' => $result['name'],
                );

                $bool = $this->model->insert($insert_values);
                $this->model->setEmoticonsCache();

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/emoticons');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/categories');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $this->view->assign('form', isset($_POST['submit']) ? $_POST : array('code' => '', 'description' => ''));

        $this->session->generateFormToken();
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/emoticons/delete', 'acp/emoticons');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = false;
            foreach ($items as $item) {
                if (!empty($item) && $this->model->resultExists($item) === true) {
                    // Datei ebenfalls löschen
                    $file = $this->model->getOneImageById($item);
                    Core\Functions::removeUploadedFile('emoticons', $file);
                    $bool = $this->model->delete($item);
                }
            }

            $this->model->setEmoticonsCache();

            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/emoticons');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEdit()
    {
        $emoticon = $this->model->getOneById((int)$this->uri->id);

        if (empty($emoticon) === false) {
            if (isset($_POST['submit']) === true) {
                try {
                    $file = array();
                    if (!empty($_FILES['picture']['name'])) {
                        $file['tmp_name'] = $_FILES['picture']['tmp_name'];
                        $file['name'] = $_FILES['picture']['name'];
                        $file['size'] = $_FILES['picture']['size'];
                    }

                    $this->model->validateEdit($_POST, $file, $this->lang);

                    $update_values = array(
                        'code' => Core\Functions::strEncode($_POST['code']),
                        'description' => Core\Functions::strEncode($_POST['description']),
                    );

                    if (empty($file) === false) {
                        Core\Functions::removeUploadedFile('emoticons', $emoticon['img']);
                        $result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'emoticons');
                        $update_values['img'] = $result['name'];
                    }

                    $bool = $this->model->update($update_values, $this->uri->id);
                    $this->model->setEmoticonsCache();

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/emoticons');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/news');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : $emoticon);

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $emoticons = $this->model->getAll();
        $c_emoticons = count($emoticons);

        if ($c_emoticons > 0) {
            $can_delete = Core\Modules::hasPermission('emoticons', 'acp_delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $can_delete === true ? 4 : 3,
                'sort_dir' => 'desc',
                'hide_col_sort' => $can_delete === true ? 0 : ''
            );
            $this->view->assign('emoticons', $emoticons);
            $this->view->assign('can_delete', $can_delete);
            $this->view->appendContent(Core\Functions::datatable($config));
        }
    }

    public function actionSettings()
    {
        if (isset($_POST['submit']) === true) {
            try {
                $this->model->validateSettings($_POST, $this->lang);

                $data = array(
                    'width' => (int)$_POST['width'],
                    'height' => (int)$_POST['height'],
                    'filesize' => (int)$_POST['filesize'],
                );
                $bool = Core\Config::setSettings('emoticons', $data);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/emoticons');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/news');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $this->view->assign('form', isset($_POST['submit']) ? $_POST : Core\Config::getSettings('emoticons'));

        $this->session->generateFormToken();
    }

}
