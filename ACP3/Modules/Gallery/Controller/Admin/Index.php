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
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Gallery\Model
     */
    protected $galleryModel;
    /**
     * @var Core\Config
     */
    protected $galleryConfig;

    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Core\Helpers\Secure $secureHelper,
        Gallery\Model $galleryModel,
        Core\Config $galleryConfig)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->galleryModel = $galleryModel;
        $this->galleryConfig = $galleryConfig;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            $this->_createPost($_POST);
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

            if (empty($_POST) === false) {
                $this->_editPost($_POST);
            }

            $this->redirectMessages()->getMessage();

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
                    'hide_col_sort' => $canDelete === true ? 0 : '',
                    'records_per_page' => $this->auth->entries
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
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->auth->entries
            );
            $this->appendContent($this->get('core.functions')->dataTable($config));

            $this->view->assign('galleries', $galleries);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    public function actionSettings()
    {
        $settings = $this->galleryConfig->getSettings();

        if (empty($_POST) === false) {
            $this->_settingsPost($_POST, $settings);
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

    private function _createPost(array $formData)
    {
        try {
            $validator = $this->get('gallery.validator');
            $validator->validateCreate($formData);

            $insertValues = array(
                'id' => '',
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'user_id' => $this->auth->getUserId(),
            );

            $lastId = $this->galleryModel->insert($insertValues);

            $this->aliases->insertUriAlias(
                sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $lastId),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
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

    private function _editPost(array $formData)
    {
        try {
            $validator = $this->get('gallery.validator');
            $validator->validateEdit($formData);

            $updateValues = array(
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'user_id' => $this->auth->getUserId(),
            );

            $bool = $this->galleryModel->update($updateValues, $this->request->id);

            $this->aliases->insertUriAlias(
                sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $this->request->id),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );
            $this->get('gallery.helpers')->generatePictureAliases($this->request->id);

            $this->seo->setCache();

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/gallery');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    private function _settingsPost(array $formData, array $settings)
    {
        try {
            $validator = $this->get('gallery.validator');
            $validator->validateSettings($formData);

            $data = array(
                'width' => (int)$formData['width'],
                'height' => (int)$formData['height'],
                'thumbwidth' => (int)$formData['thumbwidth'],
                'thumbheight' => (int)$formData['thumbheight'],
                'maxwidth' => (int)$formData['maxwidth'],
                'maxheight' => (int)$formData['maxheight'],
                'filesize' => (int)$formData['filesize'],
                'overlay' => $formData['overlay'],
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'sidebar' => (int)$formData['sidebar'],
            );
            if ($this->modules->isActive('comments') === true) {
                $data['comments'] = (int)$formData['comments'];
            }

            $bool = $this->galleryConfig->setSettings($data);

            // Falls sich die anzuzeigenden Bildgrößen geändert haben, die gecacheten Bilder löschen
            if ($formData['thumbwidth'] !== $settings['thumbwidth'] || $formData['thumbheight'] !== $settings['thumbheight'] ||
                $formData['width'] !== $settings['width'] || $formData['height'] !== $settings['height']
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

}