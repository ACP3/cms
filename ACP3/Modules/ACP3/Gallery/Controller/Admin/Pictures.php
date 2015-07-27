<?php

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Pictures
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin
 */
class Pictures extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Core\Helpers\Sort
     */
    protected $sortHelper;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helpers
     */
    protected $galleryHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model
     */
    protected $galleryModel;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Cache
     */
    protected $galleryCache;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Validator
     */
    protected $galleryValidator;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\Date                            $date
     * @param \ACP3\Core\Helpers\FormToken               $formTokenHelper
     * @param \ACP3\Core\Helpers\Sort                    $sortHelper
     * @param \ACP3\Modules\ACP3\Gallery\Helpers         $galleryHelpers
     * @param \ACP3\Modules\ACP3\Gallery\Model           $galleryModel
     * @param \ACP3\Modules\ACP3\Gallery\Cache           $galleryCache
     * @param \ACP3\Modules\ACP3\Gallery\Validator       $galleryValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Sort $sortHelper,
        Gallery\Helpers $galleryHelpers,
        Gallery\Model $galleryModel,
        Gallery\Cache $galleryCache,
        Gallery\Validator $galleryValidator)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->sortHelper = $sortHelper;
        $this->galleryHelpers = $galleryHelpers;
        $this->galleryModel = $galleryModel;
        $this->galleryCache = $galleryCache;
        $this->galleryValidator = $galleryValidator;
    }

    /**
     * @param int $id
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionCreate($id)
    {
        if ($this->galleryModel->galleryExists($id) === true) {
            $gallery = $this->galleryModel->getGalleryTitle($id);

            $this->breadcrumb
                ->append($gallery, 'acp/gallery/index/edit/id_' . $id)
                ->append($this->lang->t('gallery', 'admin_pictures_create'));

            $settings = $this->config->getSettings('gallery');

            if ($this->request->getPost()->isEmpty() === false) {
                $this->_createPost($this->request->getPost()->getAll(), $settings, $id);
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $options = [];
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = $this->get('core.helpers.forms')->selectEntry('comments', '1', '0', 'checked');
                $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            $this->view->assign('form', array_merge(['description' => ''], $this->request->getPost()->getAll()));
            $this->view->assign('gallery_id', $id);

            $this->formTokenHelper->generateFormToken($this->request->getQuery());
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($id, $action = '')
    {
        $this->handleDeleteAction(
            $action,
            function ($items) {
                $bool = false;
                foreach ($items as $item) {
                    if (!empty($item) && $this->galleryModel->pictureExists($item) === true) {
                        // Datei ebenfalls löschen
                        $picture = $this->galleryModel->getPictureById($item);
                        $this->galleryModel->updatePicturesNumbers($picture['pic'], $picture['gallery_id']);
                        $this->galleryHelpers->removePicture($picture['file']);

                        $bool = $this->galleryModel->delete($item, '', Gallery\Model::TABLE_NAME_PICTURES);
                        $this->seo->deleteUriAlias(sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $item));

                        $this->galleryCache->saveCache($picture['gallery_id']);
                    }
                }

                return $bool;
            },
            null,
            'acp/gallery/index/edit/id_' . $id
        );
    }

    /**
     * @param int $id
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        if ($this->galleryModel->pictureExists($id) === true) {
            $picture = $this->galleryModel->getPictureById($id);

            $this->breadcrumb
                ->append($picture['title'], 'acp/gallery/index/edit/id_' . $picture['gallery_id'])
                ->append($this->lang->t('gallery', 'admin_pictures_edit'))
                ->setTitlePostfix($picture['title'] . $this->breadcrumb->getTitleSeparator() . sprintf($this->lang->t('gallery', 'picture_x'), $picture['pic']));

            $settings = $this->config->getSettings('gallery');

            if ($this->request->getPost()->isEmpty() === false) {
                $this->_editPost($this->request->getPost()->getAll(), $settings, $picture, $id);
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $options = [];
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = $this->get('core.helpers.forms')->selectEntry('comments', '1', $picture['comments'], 'checked');
                $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            $this->view->assign('form', array_merge($picture, $this->request->getPost()->getAll()));
            $this->view->assign('gallery_id', $id);

            $this->formTokenHelper->generateFormToken($this->request->getQuery());
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param int    $id
     * @param string $action
     */
    public function actionOrder($id, $action)
    {
        if (($action === 'up' || $action === 'down') && $this->galleryModel->pictureExists($id) === true) {
            if ($action === 'up') {
                $this->sortHelper->up(Gallery\Model::TABLE_NAME_PICTURES, 'id', 'pic', $id, 'gallery_id');
            } else {
                $this->sortHelper->down(Gallery\Model::TABLE_NAME_PICTURES, 'id', 'pic', $id, 'gallery_id');
            }

            $galleryId = $this->galleryModel->getGalleryIdFromPictureId($id);

            $this->galleryCache->saveCache($galleryId);

            $this->redirect()->temporary('acp/gallery/index/edit/id_' . $galleryId);
        }
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param int   $id
     */
    protected function _createPost(array $formData, array $settings, $id)
    {
        $this->handleCreatePostAction(
            function () use ($formData, $settings, $id) {
                $file = $this->request->getFiles()->get('file');

                $this->galleryValidator->validateCreatePicture($file, $settings);

                $upload = new Core\Helpers\Upload('gallery');
                $result = $upload->moveFile($file['tmp_name'], $file['name']);
                $picNum = $this->galleryModel->getLastPictureByGalleryId($id);

                $insertValues = [
                    'id' => '',
                    'pic' => !is_null($picNum) ? $picNum + 1 : 1,
                    'gallery_id' => $id,
                    'file' => $result['name'],
                    'description' => Core\Functions::strEncode($formData['description'], true),
                    'comments' => $settings['comments'] == 1 ? (isset($formData['comments']) && $formData['comments'] == 1 ? 1 : 0) : $settings['comments'],
                ];

                $lastId = $this->galleryModel->insert($insertValues, Gallery\Model::TABLE_NAME_PICTURES);
                $bool2 = $this->galleryHelpers->generatePictureAlias($lastId);

                $this->galleryCache->saveCache($id);

                $this->formTokenHelper->unsetFormToken($this->request->getQuery());

                return $lastId && $bool2;
            },
            'acp/gallery/index/edit/id_' . $id
        );
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param array $picture
     * @param int   $id
     */
    protected function _editPost(array $formData, array $settings, array $picture, $id)
    {
        $this->handleEditPostAction(
            function () use ($formData, $settings, $picture, $id) {
                $file = $this->request->getFiles()->get('file');

                $this->galleryValidator->validateEditPicture($file, $settings);

                $updateValues = [
                    'description' => Core\Functions::strEncode($formData['description'], true),
                    'comments' => $settings['comments'] == 1 ? (isset($formData['comments']) && $formData['comments'] == 1 ? 1 : 0) : $settings['comments'],
                ];

                if (!empty($file)) {
                    $upload = new Core\Helpers\Upload('gallery');
                    $result = $upload->moveFile($file['tmp_name'], $file['name']);
                    $oldFile = $this->galleryModel->getFileById($id);

                    $this->galleryHelpers->removePicture($oldFile);

                    $updateValues = array_merge($updateValues, ['file' => $result['name']]);
                }

                $bool = $this->galleryModel->update($updateValues, $id, Gallery\Model::TABLE_NAME_PICTURES);

                $this->galleryCache->saveCache($picture['gallery_id']);

                $this->formTokenHelper->unsetFormToken($this->request->getQuery());

                return $bool;
            },
            'acp/gallery/index/edit/id_' . $picture['gallery_id']
        );
    }
}
