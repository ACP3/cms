<?php

namespace ACP3\Modules\Gallery\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Gallery;

/**
 * Description of GalleryAdmin
 *
 * @author Tino Goratsch
 */
class Pictures extends Core\Modules\Controller\Admin
{

    /**
     *
     * @var Gallery\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Gallery\Model($this->db);
    }

    public function actionCreate()
    {
        if ($this->model->galleryExists((int)$this->uri->id) === true) {
            $gallery = $this->model->getGalleryTitle((int)$this->uri->id);

            $this->breadcrumb
                ->append($gallery, 'acp/gallery/index/edit/id_' . $this->uri->id)
                ->append($this->lang->t('gallery', 'admin_pictures_create'));

            $config = new Core\Config($this->db, 'gallery');
            $settings = $config->getSettings();

            if (empty($_POST) === false) {
                try {
                    $file = array();
                    $file['tmp_name'] = $_FILES['file']['tmp_name'];
                    $file['name'] = $_FILES['file']['name'];
                    $file['size'] = $_FILES['file']['size'];

                    $validator = new Gallery\Validator($this->lang);
                    $validator->validateCreatePicture($file, $settings);

                    $upload = new Core\Helpers\Upload('gallery');
                    $result = $upload->moveFile($file['tmp_name'], $file['name']);
                    $picNum = $this->model->getLastPictureByGalleryId($this->uri->id);

                    $insertValues = array(
                        'id' => '',
                        'pic' => !is_null($picNum) ? $picNum + 1 : 1,
                        'gallery_id' => $this->uri->id,
                        'file' => $result['name'],
                        'description' => Core\Functions::strEncode($_POST['description'], true),
                        'comments' => $settings['comments'] == 1 ? (isset($_POST['comments']) && $_POST['comments'] == 1 ? 1 : 0) : $settings['comments'],
                    );

                    $lastId = $this->model->insert($insertValues, Gallery\Model::TABLE_NAME_PICTURES);
                    $bool2 = Gallery\Helpers::generatePictureAlias($lastId);

                    $cache = new Gallery\Cache($this->db, $this->model);
                    $cache->setCache($this->uri->id);

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($lastId && $bool2, $this->lang->t('system', $lastId !== false && $bool2 !== false ? 'create_success' : 'create_error'), 'acp/gallery/index/edit/id_' . $this->uri->id);
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/files');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                    $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
                }
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && Core\Modules::isActive('comments') === true) {
                $options = array();
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = Core\Functions::selectEntry('comments', '1', '0', 'checked');
                $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            $galleries = $this->model->getAll();
            $c_galleries = count($galleries);
            for ($i = 0; $i < $c_galleries; ++$i) {
                $galleries[$i]['selected'] = Core\Functions::selectEntry('gallery', $galleries[$i]['id'], $this->uri->id);
                $galleries[$i]['date'] = $this->date->format($galleries[$i]['start']);
            }

            $this->view->assign('galleries', $galleries);
            $this->view->assign('form', array_merge(array('description' => ''), $_POST));
            $this->view->assign('gallery_id', $this->uri->id);

            $this->session->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/gallery/pictures/delete', 'acp/gallery/index/edit/id_' . $this->uri->id);

        if ($this->uri->action === 'confirmed') {
            $cache = new Gallery\Cache($this->db, $this->model);

            $bool = false;
            foreach ($items as $item) {
                if (!empty($item) && $this->model->pictureExists($item) === true) {
                    // Datei ebenfalls löschen
                    $picture = $this->model->getPictureById($item);
                    $this->model->updatePicturesNumbers($picture['pic'], $picture['gallery_id']);
                    Gallery\Helpers::removePicture($picture['file']);

                    $bool = $this->model->delete($item, '', Gallery\Model::TABLE_NAME_PICTURES);
                    $this->uri->deleteUriAlias(sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $item));

                    $cache->setCache($picture['gallery_id']);
                }
            }

            $this->seo->setCache();

            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/gallery/index/edit/id_' . $this->uri->id);
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        if ($this->model->pictureExists((int)$this->uri->id) === true) {
            $picture = $this->model->getPictureById((int)$this->uri->id);

            $this->breadcrumb
                ->append($picture['title'], 'acp/gallery/index/edit/id_' . $picture['gallery_id'])
                ->append($this->lang->t('gallery', 'admin_pictures_edit'));

            $config = new Core\Config($this->db, 'gallery');
            $settings = $config->getSettings();

            if (empty($_POST) === false) {
                try {
                    $file = array();
                    if (!empty($_FILES['file']['tmp_name']) && $_FILES['file']['size'] > '0') {
                        $file['tmp_name'] = $_FILES['file']['tmp_name'];
                        $file['name'] = $_FILES['file']['name'];
                        $file['size'] = $_FILES['file']['size'];
                    }

                    $validator = new Gallery\Validator($this->lang);
                    $validator->validateEditPicture($file, $settings);

                    $updateValues = array(
                        'description' => Core\Functions::strEncode($_POST['description'], true),
                        'comments' => $settings['comments'] == 1 ? (isset($_POST['comments']) && $_POST['comments'] == 1 ? 1 : 0) : $settings['comments'],
                    );

                    if (!empty($file)) {
                        $upload = new Core\Helpers\Upload('gallery');
                        $result = $upload->moveFile($file['tmp_name'], $file['name']);
                        $oldFile = $this->model->getFileById($this->uri->id);

                        Gallery\Helpers::removePicture($oldFile);

                        $updateValues = array_merge($updateValues, array('file' => $result['name']));
                    }

                    $bool = $this->model->update($updateValues, $this->uri->id, Gallery\Model::TABLE_NAME_PICTURES);

                    $cache = new Gallery\Cache($this->db, $this->model);
                    $cache->setCache($picture['gallery_id']);

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery/index/edit/id_' . $picture['gallery_id']);
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/files');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                    $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
                }
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && Core\Modules::isActive('comments') === true) {
                $options = array();
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = Core\Functions::selectEntry('comments', '1', $picture['comments'], 'checked');
                $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            $this->view->assign('form', array_merge($picture, $_POST));
            $this->view->assign('gallery_id', $this->uri->id);

            $this->session->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionOrder()
    {
        if (Core\Validate::isNumber($this->uri->id) === true) {
            if (($this->uri->action === 'up' || $this->uri->action === 'down') && $this->model->pictureExists((int)$this->uri->id) === true) {
                Core\Functions::moveOneStep($this->uri->action, Gallery\Model::TABLE_NAME_PICTURES, 'id', 'pic', $this->uri->id, 'gallery_id');

                $galleryId = $this->model->getGalleryIdFromPictureId($this->uri->id);

                $cache = new Gallery\Cache($this->db, $this->model);
                $cache->setCache($galleryId);

                $this->uri->redirect('acp/gallery/index/edit/id_' . $galleryId);
            }
        }

        throw new Core\Exceptions\ResultNotExists();
    }
}