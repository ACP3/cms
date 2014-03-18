<?php

namespace ACP3\Modules\Gallery\Controller;

use ACP3\Core;
use ACP3\Modules\Gallery;

/**
 * Description of GalleryAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller\Admin
{

    /**
     *
     * @var Gallery\Model
     */
    protected $model;

    protected function _init()
    {
        $this->model = new Gallery\Model($this->db, $this->lang, $this->uri);
    }

    public function actionCreate()
    {
        if (isset($_POST['submit']) === true) {
            try {
                $this->model->validateCreate($_POST);

                $insertValues = array(
                    'id' => '',
                    'start' => $this->date->toSQL($_POST['start']),
                    'end' => $this->date->toSQL($_POST['end']),
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'user_id' => $this->auth->getUserId(),
                );

                $lastId = $this->db->insert(DB_PRE . 'gallery', $insertValues);
                if ((bool)CONFIG_SEO_ALIASES === true) {
                    $this->uri->insertUriAlias('gallery/pics/id_' . $lastId, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int)$_POST['seo_robots']);
                    $this->seo->setCache();
                }

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'acp/gallery');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/files');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        // Datumsauswahl
        $this->view->assign('publication_period', $this->date->datepicker(array('start', 'end')));

        $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields());

        $this->view->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'alias' => '', 'seo_keywords' => '', 'seo_description' => ''));

        $this->session->generateFormToken();
    }

    public function actionCreatePicture()
    {
        if ($this->model->galleryExists((int)$this->uri->id) === true) {
            $gallery = $this->model->getGalleryTitle((int)$this->uri->id);

            $this->breadcrumb
                ->append($gallery, $this->uri->route('acp/gallery/edit/id_' . $this->uri->id))
                ->append($this->lang->t('gallery', 'acp_create_picture'));

            $settings = Core\Config::getSettings('gallery');

            if (isset($_POST['submit']) === true) {
                try {
                    $file = array();
                    $file['tmp_name'] = $_FILES['file']['tmp_name'];
                    $file['name'] = $_FILES['file']['name'];
                    $file['size'] = $_FILES['file']['size'];

                    $this->model->validateCreatePicture($file, $settings);

                    $result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'gallery');
                    $picNum = $this->model->getLastPictureByGalleryId($this->uri->id);

                    $insert_values = array(
                        'id' => '',
                        'pic' => !is_null($picNum) ? $picNum + 1 : 1,
                        'gallery_id' => $this->uri->id,
                        'file' => $result['name'],
                        'description' => Core\Functions::strEncode($_POST['description'], true),
                        'comments' => $settings['comments'] == 1 ? (isset($_POST['comments']) && $_POST['comments'] == 1 ? 1 : 0) : $settings['comments'],
                    );

                    $lastId = $this->model->insert($insert_values, Model::TABLE_NAME_PICTURES);
                    $bool2 = Gallery\Helpers::generatePictureAlias($lastId);
                    $this->model->setCache($this->uri->id);

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($lastId && $bool2, $this->lang->t('system', $lastId !== false && $bool2 !== false ? 'create_success' : 'create_error'), 'acp/gallery/edit/id_' . $this->uri->id);
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/files');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
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
            $this->view->assign('form', isset($_POST['submit']) ? $_POST : array('description' => ''));
            $this->view->assign('gallery_id', $this->uri->id);

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/gallery/delete', 'acp/gallery');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = $bool2 = false;

            foreach ($items as $item) {
                if (!empty($item) && $this->model->galleryExists($item) === true) {
                    // Hochgeladene Bilder löschen
                    $pictures = $this->model->getPicturesByGalleryId($item);
                    foreach ($pictures as $row) {
                        Gallery\Helpers::removePicture($row['file']);
                    }

                    // Galerie Cache löschen
                    Core\Cache::delete('pics_id_' . $item, 'gallery');
                    $this->uri->deleteUriAlias('gallery/pics/id_' . $item);
                    Gallery\Helpers::deletePictureAliases($item);

                    // Fotogalerie mitsamt Bildern löschen
                    $bool = $this->model->delete($item);
                    $bool2 = $this->model->delete($item, 'gallery_id', Model::TABLE_NAME_PICTURES);
                }
            }

            $this->seo->setCache();

            Core\Functions::setRedirectMessage($bool && $bool2, $this->lang->t('system', $bool !== false && $bool2 !== false ? 'delete_success' : 'delete_error'), 'acp/gallery');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionDeletePicture()
    {
        $items = $this->_deleteItem('acp/gallery/delete_picture', 'acp/gallery/edit/id_' . $this->uri->id);

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = false;
            foreach ($items as $item) {
                if (!empty($item) && $this->model->pictureExists($item) === true) {
                    // Datei ebenfalls löschen
                    $picture = $this->model->getPictureById($item);
                    $this->model->updatePicturesNumbers($picture['pic'], $picture['gallery_id']);
                    Gallery\Helpers::removePicture($picture['file']);

                    $bool = $this->model->delete($item, '', Model::TABLE_NAME_PICTURES);
                    $this->uri->deleteUriAlias('gallery/details/id_' . $item);
                    $this->model->setCache($picture['gallery_id']);
                }
            }

            $this->seo->setCache();

            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/gallery/edit/id_' . $picture['gallery_id']);
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEdit()
    {
        if ($this->model->galleryExists((int)$this->uri->id) === true) {
            $gallery = $this->model->getGalleryById((int)$this->uri->id);

            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields('gallery/pics/id_' . $this->uri->id));

            $this->breadcrumb->append($gallery['title']);

            if (isset($_POST['submit']) === true) {
                try {
                    $this->model->validateEdit($_POST);

                    $updateValues = array(
                        'start' => $this->date->toSQL($_POST['start']),
                        'end' => $this->date->toSQL($_POST['end']),
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'user_id' => $this->auth->getUserId(),
                    );

                    $bool = $this->model->update($updateValues, $this->uri->id);
                    if ((bool)CONFIG_SEO_ALIASES === true) {
                        $this->uri->insertUriAlias('gallery/pics/id_' . $this->uri->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int)$_POST['seo_robots']);
                        Gallery\Helpers::generatePictureAliases($this->uri->id);

                        $this->seo->setCache();
                    }

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/files');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            Core\Functions::getRedirectMessage();

            $this->view->assign('gallery_id', $this->uri->id);

            // Datumsauswahl
            $this->view->assign('publication_period', $this->date->datepicker(array('start', 'end'), array($gallery['start'], $gallery['end'])));

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : $gallery);

            $pictures = $this->model->getPicturesByGalleryId((int)$this->uri->id);
            $c_pictures = count($pictures);

            if ($c_pictures > 0) {
                $can_delete = Core\Modules::hasPermission('gallery', 'acp_delete_picture');
                $config = array(
                    'element' => '#acp-table',
                    'hide_col_sort' => $can_delete === true ? 0 : ''
                );
                $this->view->appendContent(Core\Functions::dataTable($config));

                for ($i = 0; $i < $c_pictures; ++$i) {
                    $pictures[$i]['first'] = $i == 0 ? true : false;
                    $pictures[$i]['last'] = $i == $c_pictures - 1 ? true : false;
                }
                $this->view->assign('pictures', $pictures);
                $this->view->assign('can_delete', $can_delete);
                $this->view->assign('can_order', Core\Modules::hasPermission('gallery', 'acp_order'));
                $this->view->assign('can_edit_picture', Core\Modules::hasPermission('gallery', 'acp_edit_picture'));
            }

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEditPicture()
    {
        if ($this->model->pictureExists((int)$this->uri->id) === true) {
            $picture = $this->model->getPictureById((int)$this->uri->id);

            $this->breadcrumb
                ->append($picture['gallery_title'], $this->uri->route('acp/gallery/edit/id_' . $picture['gallery_id']))
                ->append($this->lang->t('gallery', 'acp_edit_picture'));

            $settings = Core\Config::getSettings('gallery');

            if (isset($_POST['submit']) === true) {
                try {
                    $file = array();
                    if (!empty($_FILES['file']['tmp_name']) && $_FILES['file']['size'] > '0') {
                        $file['tmp_name'] = $_FILES['file']['tmp_name'];
                        $file['name'] = $_FILES['file']['name'];
                        $file['size'] = $_FILES['file']['size'];
                    }

                    $this->model->validateEditPicture($file, $settings);

                    $updateValues = array(
                        'description' => Core\Functions::strEncode($_POST['description'], true),
                        'comments' => $settings['comments'] == 1 ? (isset($_POST['comments']) && $_POST['comments'] == 1 ? 1 : 0) : $settings['comments'],
                    );

                    if (!empty($file)) {
                        $result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'gallery');
                        $oldFile = $this->model->getFileById($this->uri->id);

                        Gallery\Helpers::removePicture($oldFile);

                        $updateValues = array_merge($updateValues, array('file' => $result['name']));
                    }

                    $bool = $this->model->update($updateValues, $this->uri->id, Model::TABLE_NAME_PICTURES);
                    $this->model->setCache($picture['gallery_id']);

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery/edit/id_' . $picture['gallery_id']);
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/files');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && Core\Modules::isActive('comments') === true) {
                $options = array();
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = Core\Functions::selectEntry('comments', '1', $picture['comments'], 'checked');
                $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : $picture);
            $this->view->assign('gallery_id', $this->uri->id);

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $galleries = $this->model->getAllInAcp();
        $c_galleries = count($galleries);

        if ($c_galleries > 0) {
            $canDelete = Core\Modules::hasPermission('gallery', 'acp_delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->view->appendContent(Core\Functions::dataTable($config));
            for ($i = 0; $i < $c_galleries; ++$i) {
                $galleries[$i]['period'] = $this->date->formatTimeRange($galleries[$i]['start'], $galleries[$i]['end']);
            }
            $this->view->assign('galleries', $galleries);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    public function actionOrder()
    {
        if (Core\Validate::isNumber($this->uri->id) === true) {
            if (($this->uri->action === 'up' || $this->uri->action === 'down') && $this->model->pictureExists((int)$this->uri->id) === true) {
                Core\Functions::moveOneStep($this->uri->action, Model::TABLE_NAME_PICTURES, 'id', 'pic', $this->uri->id, 'gallery_id');

                $galleryId = $this->model->getGalleryIdFromPictureId($this->uri->id);

                $this->model->setCache($galleryId);

                $this->uri->redirect('acp/gallery/edit/id_' . $galleryId);
            }
        }
        $this->uri->redirect('errors/404');
    }

    public function actionSettings()
    {
        $settings = Core\Config::getSettings('gallery');

        if (isset($_POST['submit']) === true) {
            try {
                $this->model->validateSettings($_POST);

                $data = array(
                    'width' => (int)$_POST['width'],
                    'height' => (int)$_POST['height'],
                    'thumbwidth' => (int)$_POST['thumbwidth'],
                    'thumbheight' => (int)$_POST['thumbheight'],
                    'maxwidth' => (int)$_POST['maxwidth'],
                    'maxheight' => (int)$_POST['maxheight'],
                    'filesize' => (int)$_POST['filesize'],
                    'overlay' => $_POST['overlay'],
                    'dateformat' => Core\Functions::strEncode($_POST['dateformat']),
                    'sidebar' => (int)$_POST['sidebar'],
                );
                if (Core\Modules::isActive('comments') === true) {
                    $data['comments'] = (int)$_POST['comments'];
                }

                $bool = Core\Config::setSettings('gallery', $data);

                // Falls sich die anzuzeigenden Bildgrößen geändert haben, die gecacheten Bilder löschen
                if ($_POST['thumbwidth'] !== $settings['thumbwidth'] || $_POST['thumbheight'] !== $settings['thumbheight'] ||
                    $_POST['width'] !== $settings['width'] || $_POST['height'] !== $settings['height']
                ) {
                    Core\Cache::purge('images', 'gallery');
                    Core\Cache::purge('sql', 'gallery');
                }

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/gallery');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/files');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        if (Core\Modules::isActive('comments') === true) {
            $lang_comments = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('comments', Core\Functions::selectGenerator('comments', array(1, 0), $lang_comments, $settings['comments'], 'checked'));
        }

        $lang_overlay = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('overlay', Core\Functions::selectGenerator('overlay', array(1, 0), $lang_overlay, $settings['overlay'], 'checked'));

        $this->view->assign('dateformat', $this->date->dateFormatDropdown($settings['dateformat']));

        $this->view->assign('sidebar_entries', Core\Functions::recordsPerPage((int)$settings['sidebar'], 1, 10));

        $this->view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

        $this->session->generateFormToken();
    }

}