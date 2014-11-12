<?php

namespace ACP3\Modules\Gallery\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Gallery;

/**
 * Class Pictures
 * @package ACP3\Modules\Gallery\Controller\Admin
 */
class Pictures extends Core\Modules\Controller\Admin
{

    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Gallery\Model
     */
    protected $galleryModel;
    /**
     * @var Gallery\Cache
     */
    protected $galleryCache;
    /**
     * @var Core\Config
     */
    protected $galleryConfig;

    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Core\Helpers\Secure $secureHelper,
        Gallery\Model $guestbookModel,
        Gallery\Cache $galleryCache,
        Core\Config $galleryConfig)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->galleryModel = $guestbookModel;
        $this->galleryCache = $galleryCache;
        $this->galleryConfig = $galleryConfig;
    }

    public function actionCreate()
    {
        if ($this->galleryModel->galleryExists((int)$this->request->id) === true) {
            $gallery = $this->galleryModel->getGalleryTitle((int)$this->request->id);

            $this->breadcrumb
                ->append($gallery, 'acp/gallery/index/edit/id_' . $this->request->id)
                ->append($this->lang->t('gallery', 'admin_pictures_create'));

            $settings = $this->galleryConfig->getSettings();

            if (empty($_POST) === false) {
                $this->_createPost($_POST, $settings);
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $options = array();
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = Core\Functions::selectEntry('comments', '1', '0', 'checked');
                $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            $this->view->assign('form', array_merge(array('description' => ''), $_POST));
            $this->view->assign('gallery_id', $this->request->id);

            $this->secureHelper->generateFormToken($this->request->query);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/gallery/pictures/delete', 'acp/gallery/index/edit/id_' . $this->request->id);

        if ($this->request->action === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                if (!empty($item) && $this->galleryModel->pictureExists($item) === true) {
                    // Datei ebenfalls löschen
                    $picture = $this->galleryModel->getPictureById($item);
                    $this->galleryModel->updatePicturesNumbers($picture['pic'], $picture['gallery_id']);
                    $this->get('gallery.helpers')->removePicture($picture['file']);

                    $bool = $this->galleryModel->delete($item, '', Gallery\Model::TABLE_NAME_PICTURES);
                    $this->aliases->deleteUriAlias(sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $item));

                    $this->galleryCache->setCache($picture['gallery_id']);
                }
            }

            $this->seo->setCache();

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
                ->append($this->lang->t('gallery', 'admin_pictures_edit'));

            $settings = $this->galleryConfig->getSettings();

            if (empty($_POST) === false) {
                $this->_editPost($_POST, $settings, $picture);
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $options = array();
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = Core\Functions::selectEntry('comments', '1', $picture['comments'], 'checked');
                $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            $this->view->assign('form', array_merge($picture, $_POST));
            $this->view->assign('gallery_id', $this->request->id);

            $this->secureHelper->generateFormToken($this->request->query);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionOrder()
    {
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->id) === true) {
            if (($this->request->action === 'up' || $this->request->action === 'down') && $this->galleryModel->pictureExists((int)$this->request->id) === true) {
                $this->get('core.functions')->moveOneStep($this->request->action, Gallery\Model::TABLE_NAME_PICTURES, 'id', 'pic', $this->request->id, 'gallery_id');

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
    private function _createPost(array $formData, array $settings)
    {
        try {
            $file = array();
            $file['tmp_name'] = $_FILES['file']['tmp_name'];
            $file['name'] = $_FILES['file']['name'];
            $file['size'] = $_FILES['file']['size'];

            $validator = $this->get('gallery.validator');
            $validator->validateCreatePicture($file, $settings);

            $upload = new Core\Helpers\Upload('gallery');
            $result = $upload->moveFile($file['tmp_name'], $file['name']);
            $picNum = $this->galleryModel->getLastPictureByGalleryId($this->request->id);

            $insertValues = array(
                'id' => '',
                'pic' => !is_null($picNum) ? $picNum + 1 : 1,
                'gallery_id' => $this->request->id,
                'file' => $result['name'],
                'description' => Core\Functions::strEncode($formData['description'], true),
                'comments' => $settings['comments'] == 1 ? (isset($formData['comments']) && $formData['comments'] == 1 ? 1 : 0) : $settings['comments'],
            );

            $lastId = $this->galleryModel->insert($insertValues, Gallery\Model::TABLE_NAME_PICTURES);
            $bool2 = $this->get('gallery.helpers')->generatePictureAlias($lastId);

            $this->galleryCache->setCache($this->request->id);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($lastId && $bool2, $this->lang->t('system', $lastId !== false && $bool2 !== false ? 'create_success' : 'create_error'), 'acp/gallery/index/edit/id_' . $this->request->id);
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/files');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param array $picture
     */
    private function _editPost(array $formData, array $settings, array $picture)
    {
        try {
            $file = array();
            if (!empty($_FILES['file']['tmp_name']) && $_FILES['file']['size'] > '0') {
                $file['tmp_name'] = $_FILES['file']['tmp_name'];
                $file['name'] = $_FILES['file']['name'];
                $file['size'] = $_FILES['file']['size'];
            }

            $validator = $this->get('gallery.validator');
            $validator->validateEditPicture($file, $settings);

            $updateValues = array(
                'description' => Core\Functions::strEncode($formData['description'], true),
                'comments' => $settings['comments'] == 1 ? (isset($formData['comments']) && $formData['comments'] == 1 ? 1 : 0) : $settings['comments'],
            );

            if (!empty($file)) {
                $upload = new Core\Helpers\Upload('gallery');
                $result = $upload->moveFile($file['tmp_name'], $file['name']);
                $oldFile = $this->galleryModel->getFileById($this->request->id);

                $this->get('gallery.helpers')->removePicture($oldFile);

                $updateValues = array_merge($updateValues, array('file' => $result['name']));
            }

            $bool = $this->galleryModel->update($updateValues, $this->request->id, Gallery\Model::TABLE_NAME_PICTURES);

            $this->galleryCache->setCache($picture['gallery_id']);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery/index/edit/id_' . $picture['gallery_id']);
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/files');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}