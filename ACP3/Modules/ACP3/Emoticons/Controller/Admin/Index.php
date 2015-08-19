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

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param array $formData
     */
    protected function _createPost(array $formData)
    {
        $this->actionHelper->handleCreatePostAction(function() use ($formData) {
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

            $this->emoticonsCache->saveCache();

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }

    /**
     * @param string $action
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function($items) {
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

                $this->emoticonsCache->saveCache();

                return $bool;
            }
        );
    }

    /**
     * @param int $id
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        $emoticon = $this->emoticonsModel->getOneById($id);

        if (empty($emoticon) === false) {
            if ($this->request->getPost()->isEmpty() === false) {
                $this->_editPost($this->request->getPost()->getAll(), $emoticon, $id);
            }

            $this->view->assign('form', array_merge($emoticon, $this->request->getPost()->getAll()));

            $this->formTokenHelper->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param array $formData
     * @param array $emoticon
     * @param int   $id
     */
    protected function _editPost(array $formData, array $emoticon, $id)
    {
        $this->actionHelper->handleEditPostAction(function() use ($formData, $emoticon, $id) {
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

            $bool = $this->emoticonsModel->update($updateValues, $id);

            $this->emoticonsCache->saveCache();

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
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
                'records_per_page' => $this->user->getEntriesPerPage()
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

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param array $formData
     */
    protected function _settingsPost(array $formData)
    {
        $this->actionHelper->handleSettingsPostAction(function() use ($formData){
            $this->emoticonsValidator->validateSettings($formData);

            $data = [
                'width' => (int)$formData['width'],
                'height' => (int)$formData['height'],
                'filesize' => (int)$formData['filesize'],
            ];

            $this->formTokenHelper->unsetFormToken();

            return $this->config->setSettings($data, 'emoticons');
        });
    }
}
