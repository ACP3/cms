<?php

namespace ACP3\Modules\Gallery\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Gallery;

/**
 * Class Index
 * @package ACP3\Modules\Gallery\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{

    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Gallery\Model
     */
    protected $galleryModel;

    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Core\Helpers\Secure $secureHelper,
        Gallery\Model $galleryModel)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->db = $db;
        $this->secureHelper = $secureHelper;
        $this->galleryModel = $galleryModel;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            try {
                $validator = $this->get('gallery.validator');
                $validator->validateCreate($_POST);

                $insertValues = array(
                    'id' => '',
                    'start' => $this->date->toSQL($_POST['start']),
                    'end' => $this->date->toSQL($_POST['end']),
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'user_id' => $this->auth->getUserId(),
                );

                $lastId = $this->galleryModel->insert($insertValues);

                $this->aliases->insertUriAlias(
                    sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $lastId),
                    $_POST['alias'],
                    $_POST['seo_keywords'],
                    $_POST['seo_description'],
                    (int)$_POST['seo_robots']
                );
                $this->seo->setCache();

                $this->secureHelper->unsetFormToken($this->request->query);

                $this->redirectMessages()->setMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'acp/gallery');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/gallery');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
            }
        }

        // Datumsauswahl
        $this->view->assign('publication_period', $this->date->datepicker(array('start', 'end')));

        $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields());

        $defaults = array(
            'title' => '',
            'alias' => '',
            'seo_keywords' => '',
            'seo_description' => ''
        );
        $this->view->assign('form', array_merge($defaults, $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/gallery/index/delete', 'acp/gallery');

        if ($this->request->action === 'confirmed') {
            $bool = $bool2 = false;

            $cache = new Core\Cache('gallery');

            foreach ($items as $item) {
                if (!empty($item) && $this->galleryModel->galleryExists($item) === true) {
                    // Hochgeladene Bilder löschen
                    $pictures = $this->galleryModel->getPicturesByGalleryId($item);
                    foreach ($pictures as $row) {
                        $this->get('gallery.helpers')->removePicture($row['file']);
                    }

                    // Galerie Cache löschen
                    $cache->delete(Gallery\Cache::CACHE_ID . $item);
                    $this->aliases->deleteUriAlias(sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $item));
                    $this->get('gallery.helpers')->deletePictureAliases($item);

                    // Fotogalerie mitsamt Bildern löschen
                    $bool = $this->galleryModel->delete($item);
                    $bool2 = $this->galleryModel->delete($item, 'gallery_id', Gallery\Model::TABLE_NAME_PICTURES);
                }
            }

            $this->seo->setCache();

            $this->redirectMessages()->setMessage($bool && $bool2, $this->lang->t('system', $bool !== false && $bool2 !== false ? 'delete_success' : 'delete_error'), 'acp/gallery');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        if ($this->galleryModel->galleryExists((int)$this->request->id) === true) {
            $gallery = $this->galleryModel->getGalleryById((int)$this->request->id);

            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields(sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $this->request->id)));

            $this->breadcrumb->append($gallery['title']);

            $redirect = $this->redirectMessages();

            if (empty($_POST) === false) {
                try {
                    $validator = $this->get('gallery.validator');
                    $validator->validateEdit($_POST);

                    $updateValues = array(
                        'start' => $this->date->toSQL($_POST['start']),
                        'end' => $this->date->toSQL($_POST['end']),
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'user_id' => $this->auth->getUserId(),
                    );

                    $bool = $this->galleryModel->update($updateValues, $this->request->id);

                    $this->aliases->insertUriAlias(
                        sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $this->request->id),
                        $_POST['alias'],
                        $_POST['seo_keywords'],
                        $_POST['seo_description'],
                        (int)$_POST['seo_robots']
                    );
                    $this->get('gallery.helpers')->generatePictureAliases($this->request->id);

                    $this->seo->setCache();

                    $this->secureHelper->unsetFormToken($this->request->query);

                    $redirect->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    $redirect->setMessage(false, $e->getMessage(), 'acp/gallery');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
                }
            }

            $redirect->getMessage();

            $this->view->assign('gallery_id', $this->request->id);

            // Datumsauswahl
            $this->view->assign('publication_period', $this->date->datepicker(array('start', 'end'), array($gallery['start'], $gallery['end'])));

            $this->view->assign('form', array_merge($gallery, $_POST));

            $pictures = $this->galleryModel->getPicturesByGalleryId((int)$this->request->id);
            $c_pictures = count($pictures);

            if ($c_pictures > 0) {
                $canDelete = $this->modules->hasPermission('admin/gallery/pictures/delete');
                $config = array(
                    'element' => '#acp-table',
                    'hide_col_sort' => $canDelete === true ? 0 : ''
                );
                $this->appendContent($this->get('core.functions')->dataTable($config));

                for ($i = 0; $i < $c_pictures; ++$i) {
                    $pictures[$i]['first'] = $i == 0;
                    $pictures[$i]['last'] = $i == $c_pictures - 1;
                }
                $this->view->assign('pictures', $pictures);
                $this->view->assign('can_delete', $canDelete);
                $this->view->assign('can_order', $this->modules->hasPermission('admin/gallery/pictures/order'));
                $this->view->assign('can_edit_picture', $this->modules->hasPermission('admin/gallery/pictures/edit'));
            }

            $this->secureHelper->generateFormToken($this->request->query);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $this->redirectMessages()->getMessage();

        $galleries = $this->galleryModel->getAllInAcp();
        $c_galleries = count($galleries);

        if ($c_galleries > 0) {
            $canDelete = $this->modules->hasPermission('admin/gallery/index/delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->appendContent($this->get('core.functions')->dataTable($config));
            for ($i = 0; $i < $c_galleries; ++$i) {
                $galleries[$i]['period'] = $this->date->formatTimeRange($galleries[$i]['start'], $galleries[$i]['end']);
            }
            $this->view->assign('galleries', $galleries);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    public function actionSettings()
    {
        $config = new Core\Config($this->db, 'gallery');
        $settings = $config->getSettings();

        if (empty($_POST) === false) {
            try {
                $validator = $this->get('gallery.validator');
                $validator->validateSettings($_POST);

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
                if ($this->modules->isActive('comments') === true) {
                    $data['comments'] = (int)$_POST['comments'];
                }

                $bool = $config->setSettings($data);

                // Falls sich die anzuzeigenden Bildgrößen geändert haben, die gecacheten Bilder löschen
                if ($_POST['thumbwidth'] !== $settings['thumbwidth'] || $_POST['thumbheight'] !== $settings['thumbheight'] ||
                    $_POST['width'] !== $settings['width'] || $_POST['height'] !== $settings['height']
                ) {
                    Core\Cache::purge(UPLOADS_DIR . 'cache/images', 'gallery');

                    $cache = new Core\Cache('gallery');
                    $cache->getDriver()->deleteAll();
                }

                $this->secureHelper->unsetFormToken($this->request->query);

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/gallery');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/gallery');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
            }
        }

        if ($this->modules->isActive('comments') === true) {
            $lang_comments = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('comments', Core\Functions::selectGenerator('comments', array(1, 0), $lang_comments, $settings['comments'], 'checked'));
        }

        $lang_overlay = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('overlay', Core\Functions::selectGenerator('overlay', array(1, 0), $lang_overlay, $settings['overlay'], 'checked'));

        $this->view->assign('dateformat', $this->date->dateFormatDropdown($settings['dateformat']));

        $this->view->assign('sidebar_entries', Core\Functions::recordsPerPage((int)$settings['sidebar'], 1, 10));

        $this->view->assign('form', array_merge($settings, $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

}