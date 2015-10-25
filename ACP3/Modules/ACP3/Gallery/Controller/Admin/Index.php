<?php

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin
 */
class Index extends Core\Modules\AdminController
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
     * @var \ACP3\Modules\ACP3\Gallery\Cache
     */
    protected $galleryCache;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helpers
     */
    protected $galleryHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository
     */
    protected $galleryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Validator\Gallery
     */
    protected $galleryValidator;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Validator\Settings
     */
    protected $settingsValidator;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\PictureRepository
     */
    protected $pictureRepository;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext         $context
     * @param \ACP3\Core\Date                                    $date
     * @param \ACP3\Core\Helpers\FormToken                       $formTokenHelper
     * @param \ACP3\Modules\ACP3\Gallery\Cache                   $galleryCache
     * @param \ACP3\Modules\ACP3\Gallery\Helpers                 $galleryHelpers
     * @param \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository $galleryRepository
     * @param \ACP3\Modules\ACP3\Gallery\Model\PictureRepository $pictureRepository
     * @param \ACP3\Modules\ACP3\Gallery\Validator\Gallery       $galleryValidator
     * @param \ACP3\Modules\ACP3\Gallery\Validator\Settings      $settingsValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Gallery\Cache $galleryCache,
        Gallery\Helpers $galleryHelpers,
        Gallery\Model\GalleryRepository $galleryRepository,
        Gallery\Model\PictureRepository $pictureRepository,
        Gallery\Validator\Gallery $galleryValidator,
        Gallery\Validator\Settings $settingsValidator)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->galleryCache = $galleryCache;
        $this->galleryHelpers = $galleryHelpers;
        $this->galleryRepository = $galleryRepository;
        $this->pictureRepository = $pictureRepository;
        $this->galleryValidator = $galleryValidator;
        $this->settingsValidator = $settingsValidator;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_createPost($this->request->getPost()->all());
        }

        $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields());

        $defaults = [
            'title' => '',
            'start' => '',
            'end' => ''
        ];
        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->all()));

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param string $action
     *
     * @return mixed
     */
    public function actionDelete($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;

                foreach ($items as $item) {
                    if (!empty($item) && $this->galleryRepository->galleryExists($item) === true) {
                        // Hochgeladene Bilder löschen
                        $pictures = $this->pictureRepository->getPicturesByGalleryId($item);
                        foreach ($pictures as $row) {
                            $this->galleryHelpers->removePicture($row['file']);
                        }

                        // Galerie Cache löschen
                        $this->galleryCache->getCacheDriver()->delete(Gallery\Cache::CACHE_ID . $item);
                        $this->seo->deleteUriAlias(sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $item));
                        $this->galleryHelpers->deletePictureAliases($item);

                        // Fotogalerie mitsamt Bildern löschen
                        $bool = $this->galleryRepository->delete($item);
                    }
                }

                return $bool !== false;
            }
        );
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        if ($this->galleryRepository->galleryExists($id) === true) {
            $gallery = $this->galleryRepository->getGalleryById($id);

            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields(sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $id)));

            $this->breadcrumb->setTitlePostfix($gallery['title']);

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->_editPost($this->request->getPost()->all(), $id);
            }

            $this->view->assign('gallery_id', $id);
            $this->view->assign('form', array_merge($gallery, $this->request->getPost()->all()));

            $this->_actionEditPictures($id);

            $this->formTokenHelper->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param int $id
     */
    protected function _actionEditPictures($id)
    {
        $pictures = $this->pictureRepository->getPicturesByGalleryId($id);
        $c_pictures = count($pictures);

        if ($c_pictures > 0) {
            $canDelete = $this->acl->hasPermission('admin/gallery/pictures/delete');

            for ($i = 0; $i < $c_pictures; ++$i) {
                $pictures[$i]['first'] = $i == 0;
                $pictures[$i]['last'] = $i == $c_pictures - 1;
            }
            $this->view->assign('pictures', $pictures);
            $this->view->assign('can_delete', $canDelete);
            $this->view->assign('can_order', $this->acl->hasPermission('admin/gallery/pictures/order'));
            $this->view->assign('can_edit_picture', $this->acl->hasPermission('admin/gallery/pictures/edit'));
        }
    }

    public function actionIndex()
    {
        $galleries = $this->galleryRepository->getAllInAcp();

        if (count($galleries) > 0) {
            $canDelete = $this->acl->hasPermission('admin/gallery/index/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->user->getEntriesPerPage()
            ];
            $this->view->assign('datatable_config', $config);
            $this->view->assign('galleries', $galleries);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionSettings()
    {
        $settings = $this->config->getSettings('gallery');

        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_settingsPost($this->request->getPost()->all(), $settings);
        }

        if ($this->modules->isActive('comments') === true) {
            $this->view->assign('comments', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('comments', $settings['comments']));
        }

        $this->view->assign('overlay', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('overlay', $settings['overlay']));

        $this->view->assign('dateformat', $this->get('core.helpers.date')->dateFormatDropdown($settings['dateformat']));

        $this->view->assign('sidebar_entries', $this->get('core.helpers.forms')->recordsPerPage((int)$settings['sidebar'], 1, 10));

        $this->view->assign('form', array_merge($settings, $this->request->getPost()->all()));

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _createPost(array $formData)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData) {
            $this->galleryValidator->validate($formData);

            $insertValues = [
                'id' => '',
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'user_id' => $this->user->getUserId(),
            ];

            $lastId = $this->galleryRepository->insert($insertValues);

            $this->seo->insertUriAlias(
                sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $lastId),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->formTokenHelper->unsetFormToken();

            return $lastId;
        });
    }

    /**
     * @param array $formData
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _editPost(array $formData, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $id) {
            $this->galleryValidator->validate(
                $formData,
                sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $id)
            );

            $updateValues = [
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'user_id' => $this->user->getUserId(),
            ];

            $bool = $this->galleryRepository->update($updateValues, $id);

            $this->seo->insertUriAlias(
                sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $id),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );
            $this->galleryHelpers->generatePictureAliases($id);

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _settingsPost(array $formData, array $settings)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData, $settings) {
            $this->settingsValidator->validate($formData);

            $data = [
                'width' => (int)$formData['width'],
                'height' => (int)$formData['height'],
                'thumbwidth' => (int)$formData['thumbwidth'],
                'thumbheight' => (int)$formData['thumbheight'],
                'overlay' => $formData['overlay'],
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'sidebar' => (int)$formData['sidebar'],
            ];
            if ($this->modules->isActive('comments') === true) {
                $data['comments'] = (int)$formData['comments'];
            }

            $this->formTokenHelper->unsetFormToken();

            $bool = $this->config->setSettings($data, 'gallery');

            // Falls sich die anzuzeigenden Bildgrößen geändert haben, die gecacheten Bilder löschen
            if ($formData['thumbwidth'] !== $settings['thumbwidth'] || $formData['thumbheight'] !== $settings['thumbheight'] ||
                $formData['width'] !== $settings['width'] || $formData['height'] !== $settings['height']
            ) {
                Core\Cache::purge(CACHE_DIR . 'images', 'gallery');

                $this->get('gallery.cache.core')->getDriver()->deleteAll();
            }

            return $bool;
        });
    }
}
