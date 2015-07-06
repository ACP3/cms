<?php

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Pictures
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin
 */
class Pictures extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
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
     * @param \ACP3\Core\Context\Admin        $context
     * @param \ACP3\Core\Date                 $date
     * @param \ACP3\Core\Helpers\Secure       $secureHelper
     * @param \ACP3\Core\Helpers\Sort         $sortHelper
     * @param \ACP3\Modules\ACP3\Gallery\Helpers   $galleryHelpers
     * @param \ACP3\Modules\ACP3\Gallery\Model     $galleryModel
     * @param \ACP3\Modules\ACP3\Gallery\Cache     $galleryCache
     * @param \ACP3\Modules\ACP3\Gallery\Validator $galleryValidator
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Core\Helpers\Secure $secureHelper,
        Core\Helpers\Sort $sortHelper,
        Gallery\Helpers $galleryHelpers,
        Gallery\Model $galleryModel,
        Gallery\Cache $galleryCache,
        Gallery\Validator $galleryValidator)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->sortHelper = $sortHelper;
        $this->galleryHelpers = $galleryHelpers;
        $this->galleryModel = $galleryModel;
        $this->galleryCache = $galleryCache;
        $this->galleryValidator = $galleryValidator;
    }

    public function actionCreate()
    {
        if ($this->galleryModel->galleryExists((int)$this->request->id) === true) {
            $gallery = $this->galleryModel->getGalleryTitle((int)$this->request->id);

            $this->breadcrumb
                ->append($gallery, 'acp/gallery/index/edit/id_' . $this->request->id)
                ->append($this->lang->t('gallery', 'admin_pictures_create'));

            $settings = $this->config->getSettings('gallery');

            if (empty($_POST) === false) {
                $this->_createPost($_POST, $settings);
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $options = [];
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = $this->get('core.helpers.forms')->selectEntry('comments', '1', '0', 'checked');
                $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            $this->view->assign('form', array_merge(['description' => ''], $_POST));
            $this->view->assign('gallery_id', $this->request->id);

            $this->secureHelper->generateFormToken($this->request->getQuery());
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem(null, 'acp/gallery/index/edit/id_' . $this->request->id);

        if ($this->request->action === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                if (!empty($item) && $this->galleryModel->pictureExists($item) === true) {
                    // Datei ebenfalls löschen
                    $picture = $this->galleryModel->getPictureById($item);
                    $this->galleryModel->updatePicturesNumbers($picture['pic'], $picture['gallery_id']);
                    $this->galleryHelpers->removePicture($picture['file']);

                    $bool = $this->galleryModel->delete($item, '', Gallery\Model::TABLE_NAME_PICTURES);
                    $this->seo->deleteUriAlias(sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $item));

                    $this->galleryCache->setCache($picture['gallery_id']);
                }
            }

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/gallery/index/edit/id_' . $this->request->id);
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        if ($this->galleryModel->pictureExists((int)$this->request->id) === true) {
            $picture = $this->galleryModel->getPictureById((int)$this->request->id);

            $this->breadcrumb
                ->append($picture['title'], 'acp/gallery/index/edit/id_' . $picture['gallery_id'])
                ->append($this->lang->t('gallery', 'admin_pictures_edit'))
                ->setTitlePostfix($picture['title'] . $this->breadcrumb->getTitleSeparator() . sprintf($this->lang->t('gallery', 'picture_x'), $picture['pic']));

            $settings = $this->config->getSettings('gallery');

            if (empty($_POST) === false) {
                $this->_editPost($_POST, $settings, $picture);
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $options = [];
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = $this->get('core.helpers.forms')->selectEntry('comments', '1', $picture['comments'], 'checked');
                $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            $this->view->assign('form', array_merge($picture, $_POST));
            $this->view->assign('gallery_id', $this->request->id);

            $this->secureHelper->generateFormToken($this->request->getQuery());
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionOrder()
    {
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->id) === true) {
            if (($this->request->action === 'up' || $this->request->action === 'down') && $this->galleryModel->pictureExists((int)$this->request->id) === true) {
                if ($this->request->action === 'up') {
                    $this->sortHelper->up(Gallery\Model::TABLE_NAME_PICTURES, 'id', 'pic', $this->request->id, 'gallery_id');
                } else {
                    $this->sortHelper->down(Gallery\Model::TABLE_NAME_PICTURES, 'id', 'pic', $this->request->id, 'gallery_id');
                }

                $galleryId = $this->galleryModel->getGalleryIdFromPictureId($this->request->id);

                $this->galleryCache->setCache($galleryId);

                $this->redirect()->temporary('acp/gallery/index/edit/id_' . $galleryId);
            }
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array $formData
     * @param array $settings
     */
    protected function _createPost(array $formData, array $settings)
    {
        try {
            $file = [];
            $file['tmp_name'] = $_FILES['file']['tmp_name'];
            $file['name'] = $_FILES['file']['name'];
            $file['size'] = $_FILES['file']['size'];

            $this->galleryValidator->validateCreatePicture($file, $settings);

            $upload = new Core\Helpers\Upload('gallery');
            $result = $upload->moveFile($file['tmp_name'], $file['name']);
            $picNum = $this->galleryModel->getLastPictureByGalleryId($this->request->id);

            $insertValues = [
                'id' => '',
                'pic' => !is_null($picNum) ? $picNum + 1 : 1,
                'gallery_id' => $this->request->id,
                'file' => $result['name'],
                'description' => Core\Functions::strEncode($formData['description'], true),
                'comments' => $settings['comments'] == 1 ? (isset($formData['comments']) && $formData['comments'] == 1 ? 1 : 0) : $settings['comments'],
            ];

            $lastId = $this->galleryModel->insert($insertValues, Gallery\Model::TABLE_NAME_PICTURES);
            $bool2 = $this->galleryHelpers->generatePictureAlias($lastId);

            $this->galleryCache->setCache($this->request->id);

            $this->secureHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($lastId && $bool2, $this->lang->t('system', $lastId !== false && $bool2 !== false ? 'create_success' : 'create_error'), 'acp/gallery/index/edit/id_' . $this->request->id);
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/gallery/index/edit/id_' . $this->request->id);
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param array $picture
     */
    protected function _editPost(array $formData, array $settings, array $picture)
    {
        try {
            $file = [];
            if (!empty($_FILES['file']['tmp_name']) && $_FILES['file']['size'] > '0') {
                $file['tmp_name'] = $_FILES['file']['tmp_name'];
                $file['name'] = $_FILES['file']['name'];
                $file['size'] = $_FILES['file']['size'];
            }

            $this->galleryValidator->validateEditPicture($file, $settings);

            $updateValues = [
                'description' => Core\Functions::strEncode($formData['description'], true),
                'comments' => $settings['comments'] == 1 ? (isset($formData['comments']) && $formData['comments'] == 1 ? 1 : 0) : $settings['comments'],
            ];

            if (!empty($file)) {
                $upload = new Core\Helpers\Upload('gallery');
                $result = $upload->moveFile($file['tmp_name'], $file['name']);
                $oldFile = $this->galleryModel->getFileById($this->request->id);

                $this->galleryHelpers->removePicture($oldFile);

                $updateValues = array_merge($updateValues, ['file' => $result['name']]);
            }

            $bool = $this->galleryModel->update($updateValues, $this->request->id, Gallery\Model::TABLE_NAME_PICTURES);

            $this->galleryCache->setCache($picture['gallery_id']);

            $this->secureHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery/index/edit/id_' . $picture['gallery_id']);
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/gallery/index/edit/id_' . $picture['gallery_id']);
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
