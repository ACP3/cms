<?php

namespace ACP3\Modules\Emoticons\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Emoticons;

/**
 * Class Index
 * @package ACP3\Modules\Emoticons\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var Emoticons\Model
     */
    protected $emoticonsModel;
    /**
     * @var \ACP3\Core\Config
     */
    protected $emoticonsConfig;
    /**
     * @var \ACP3\Modules\Emoticons\Cache
     */
    protected $emoticonsCache;

    public function __construct(
        Core\Context $context,
        Core\Breadcrumb $breadcrumb,
        Core\SEO $seo,
        Core\Validate $validate,
        Core\Session $session,
        Emoticons\Model $emoticonsModel,
        Core\Config $emoticonsConfig,
        Emoticons\Cache $emoticonsCache)
    {
        parent::__construct($context, $breadcrumb, $seo, $validate, $session);

        $this->emoticonsModel = $emoticonsModel;
        $this->emoticonsConfig = $emoticonsConfig;
        $this->emoticonsCache = $emoticonsCache;
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

                $validator = $this->get('emoticons.validator');
                $validator->validateCreate($_POST, $file, $this->emoticonsConfig->getSettings());

                $upload = new Core\Helpers\Upload('emoticons');
                $result = $upload->moveFile($file['tmp_name'], $file['name']);

                $insertValues = array(
                    'id' => '',
                    'code' => Core\Functions::strEncode($_POST['code']),
                    'description' => Core\Functions::strEncode($_POST['description']),
                    'img' => $result['name'],
                );

                $bool = $this->emoticonsModel->insert($insertValues);

                $this->emoticonsCache->setCache();

                $this->session->unsetFormToken();

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/emoticons');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/categories');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
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

            $upload = new Core\Helpers\Upload('emoticons');
            foreach ($items as $item) {
                if (!empty($item) && $this->emoticonsModel->resultExists($item) === true) {
                    // Datei ebenfalls löschen
                    $file = $this->emoticonsModel->getOneImageById($item);
                    $upload->removeUploadedFile($file);
                    $bool = $this->emoticonsModel->delete($item);
                }
            }

            $this->emoticonsCache->setCache();

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/emoticons');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $emoticon = $this->emoticonsModel->getOneById((int)$this->uri->id);

        if (empty($emoticon) === false) {
            if (empty($_POST) === false) {
                try {
                    $file = array();
                    if (!empty($_FILES['picture']['name'])) {
                        $file['tmp_name'] = $_FILES['picture']['tmp_name'];
                        $file['name'] = $_FILES['picture']['name'];
                        $file['size'] = $_FILES['picture']['size'];
                    }

                    $validator = $this->get('emoticons.validator');
                    $validator->validateEdit($_POST, $file, $this->emoticonsConfig->getSettings());

                    $updateValues = array(
                        'code' => Core\Functions::strEncode($_POST['code']),
                        'description' => Core\Functions::strEncode($_POST['description']),
                    );

                    if (empty($file) === false) {
                        $upload = new Core\Helpers\Upload('emoticons');
                        $upload->removeUploadedFile($emoticon['img']);
                        $result = $upload->moveFile($file['tmp_name'], $file['name']);
                        $updateValues['img'] = $result['name'];
                    }

                    $bool = $this->emoticonsModel->update($updateValues, $this->uri->id);

                    $this->emoticonsCache->setCache();

                    $this->session->unsetFormToken();

                    $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/emoticons');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/news');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                    $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
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
        $this->redirectMessages()->getMessage();

        $emoticons = $this->emoticonsModel->getAll();
        $c_emoticons = count($emoticons);

        if ($c_emoticons > 0) {
            $canDelete = $this->modules->hasPermission('admin/emoticons/index/delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 4 : 3,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->view->assign('emoticons', $emoticons);
            $this->view->assign('can_delete', $canDelete);
            $this->appendContent($this->get('core.functions')->dataTable($config));
        }
    }

    public function actionSettings()
    {
        $config = $this->emoticonsConfig;

        if (empty($_POST) === false) {
            try {
                $validator = $this->get('emoticons.validator');
                $validator->validateSettings($_POST);

                $data = array(
                    'width' => (int)$_POST['width'],
                    'height' => (int)$_POST['height'],
                    'filesize' => (int)$_POST['filesize'],
                );
                $bool = $config->setSettings($data);

                $this->session->unsetFormToken();

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/emoticons');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/emoticons');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        $this->view->assign('form', array_merge($config->getSettings(), $_POST));

        $this->session->generateFormToken();
    }

}
