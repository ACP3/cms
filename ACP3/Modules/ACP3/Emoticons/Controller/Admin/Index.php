<?php

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Emoticons\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Model
     */
    protected $emoticonsModel;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Validator
     */
    protected $emoticonsValidator;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Cache
     */
    protected $emoticonsCache;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\Helpers\FormToken               $formTokenHelper
     * @param \ACP3\Modules\ACP3\Emoticons\Model         $emoticonsModel
     * @param \ACP3\Modules\ACP3\Emoticons\Validator     $emoticonsValidator
     * @param \ACP3\Modules\ACP3\Emoticons\Cache         $emoticonsCache
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Emoticons\Model $emoticonsModel,
        Emoticons\Validator $emoticonsValidator,
        Emoticons\Cache $emoticonsCache)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->emoticonsModel = $emoticonsModel;
        $this->emoticonsValidator = $emoticonsValidator;
        $this->emoticonsCache = $emoticonsCache;
    }

    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_createPost($this->request->getPost()->getAll());
        }

        $this->view->assign('form', array_merge(['code' => '', 'description' => ''], $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    /**
     * @param array $formData
     */
    protected function _createPost(array $formData)
    {
        try {
            $file = $this->request->getFiles()->get('picture');

            $this->emoticonsValidator->validateCreate($formData, $file, $this->config->getSettings('emoticons'));

            $upload = new Core\Helpers\Upload('emoticons');
            $result = $upload->moveFile($file['tmp_name'], $file['name']);

            $insertValues = [
                'id' => '',
                'code' => Core\Functions::strEncode($formData['code']),
                'description' => Core\Functions::strEncode($formData['description']),
                'img' => $result['name'],
            ];

            $bool = $this->emoticonsModel->insert($insertValues);

            $this->emoticonsCache->setCache();

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem();

        if ($this->request->getParameters()->get('action') === 'confirmed') {
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

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'));
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $emoticon = $this->emoticonsModel->getOneById((int)$this->request->getParameters()->get('id'));

        if (empty($emoticon) === false) {
            if ($this->request->getPost()->isEmpty() === false) {
                $this->_editPost($this->request->getPost()->getAll(), $emoticon);
            }

            $this->view->assign('form', array_merge($emoticon, $this->request->getPost()->getAll()));

            $this->formTokenHelper->generateFormToken($this->request->getQuery());
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param array $formData
     * @param array $emoticon
     */
    protected function _editPost(array $formData, array $emoticon)
    {
        try {
            $file = $this->request->getFiles()->get('picture');

            $this->emoticonsValidator->validateEdit($formData, $file, $this->config->getSettings('emoticons'));

            $updateValues = [
                'code' => Core\Functions::strEncode($formData['code']),
                'description' => Core\Functions::strEncode($formData['description']),
            ];

            if (empty($file) === false) {
                $upload = new Core\Helpers\Upload('emoticons');
                $upload->removeUploadedFile($emoticon['img']);
                $result = $upload->moveFile($file['tmp_name'], $file['name']);
                $updateValues['img'] = $result['name'];
            }

            $bool = $this->emoticonsModel->update($updateValues, $this->request->getParameters()->get('id'));

            $this->emoticonsCache->setCache();

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    public function actionIndex()
    {
        $emoticons = $this->emoticonsModel->getAll();

        if (count($emoticons) > 0) {
            $canDelete = $this->acl->hasPermission('admin/emoticons/index/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 4 : 3,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->auth->entries
            ];
            $this->view->assign('datatable_config', $config);
            $this->view->assign('emoticons', $emoticons);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    public function actionSettings()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_settingsPost($this->request->getPost()->getAll());
        }

        $this->view->assign('form', array_merge($this->config->getSettings('emoticons'), $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    /**
     * @param array $formData
     */
    protected function _settingsPost(array $formData)
    {
        try {
            $this->emoticonsValidator->validateSettings($formData);

            $data = [
                'width' => (int)$formData['width'],
                'height' => (int)$formData['height'],
                'filesize' => (int)$formData['filesize'],
            ];
            $bool = $this->config->setSettings($data, 'emoticons');

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
