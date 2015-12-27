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
     * @var \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository
     */
    protected $galleryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Cache
     */
    protected $galleryCache;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Validation\PictureFormValidation
     */
    protected $pictureFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\PictureRepository
     */
    protected $pictureRepository;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext                  $context
     * @param \ACP3\Core\Date                                             $date
     * @param \ACP3\Core\Helpers\FormToken                                $formTokenHelper
     * @param \ACP3\Core\Helpers\Sort                                     $sortHelper
     * @param \ACP3\Modules\ACP3\Gallery\Helpers                          $galleryHelpers
     * @param \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository          $galleryRepository
     * @param \ACP3\Modules\ACP3\Gallery\Model\PictureRepository          $pictureRepository
     * @param \ACP3\Modules\ACP3\Gallery\Cache                            $galleryCache
     * @param \ACP3\Modules\ACP3\Gallery\Validation\PictureFormValidation $pictureFormValidation
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Sort $sortHelper,
        Gallery\Helpers $galleryHelpers,
        Gallery\Model\GalleryRepository $galleryRepository,
        Gallery\Model\PictureRepository $pictureRepository,
        Gallery\Cache $galleryCache,
        Gallery\Validation\PictureFormValidation $pictureFormValidation
    )
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->sortHelper = $sortHelper;
        $this->galleryHelpers = $galleryHelpers;
        $this->galleryRepository = $galleryRepository;
        $this->pictureRepository = $pictureRepository;
        $this->galleryCache = $galleryCache;
        $this->pictureFormValidation = $pictureFormValidation;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionCreate($id)
    {
        if ($this->galleryRepository->galleryExists($id) === true) {
            $gallery = $this->galleryRepository->getGalleryTitle($id);

            $this->breadcrumb
                ->append($gallery, 'acp/gallery/index/edit/id_' . $id)
                ->append($this->translator->t('gallery', 'admin_pictures_create'));

            $settings = $this->config->getSettings('gallery');

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->_createPost($this->request->getPost()->all(), $settings, $id);
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $options = [];
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = $this->get('core.helpers.forms')->selectEntry('comments', '1', '0', 'checked');
                $options[0]['lang'] = $this->translator->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            $this->formTokenHelper->generateFormToken();

            return [
                'form' => array_merge(['description' => ''], $this->request->getPost()->all()),
                'gallery_id' => $id
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($id, $action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;
                foreach ($items as $item) {
                    if (!empty($item) && $this->pictureRepository->pictureExists($item) === true) {
                        // Datei ebenfalls löschen
                        $picture = $this->pictureRepository->getPictureById($item);
                        $this->pictureRepository->updatePicturesNumbers($picture['pic'], $picture['gallery_id']);
                        $this->galleryHelpers->removePicture($picture['file']);

                        $bool = $this->pictureRepository->delete($item);
                        $this->seo->deleteUriAlias(sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $item));

                        $this->galleryCache->saveCache($picture['gallery_id']);
                    }
                }

                return $bool;
            },
            'acp/gallery/pictures/delete/id_' . $id,
            'acp/gallery/index/edit/id_' . $id
        );
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        if ($this->pictureRepository->pictureExists($id) === true) {
            $picture = $this->pictureRepository->getPictureById($id);

            $this->breadcrumb
                ->append($picture['title'], 'acp/gallery/index/edit/id_' . $picture['gallery_id'])
                ->append($this->translator->t('gallery', 'admin_pictures_edit'))
                ->setTitlePostfix($picture['title'] . $this->breadcrumb->getTitleSeparator() . $this->translator->t('gallery',
                        'picture_x', ['%picture%' => $picture['pic']]));

            $settings = $this->config->getSettings('gallery');

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->_editPost($this->request->getPost()->all(), $settings, $picture, $id);
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $options = [];
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = $this->get('core.helpers.forms')->selectEntry('comments', '1', $picture['comments'], 'checked');
                $options[0]['lang'] = $this->translator->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            $this->formTokenHelper->generateFormToken();

            return [
                'form' => array_merge($picture, $this->request->getPost()->all()),
                'gallery_id' => $id
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionOrder($id, $action)
    {
        if (($action === 'up' || $action === 'down') && $this->pictureRepository->pictureExists($id) === true) {
            if ($action === 'up') {
                $this->sortHelper->up(Gallery\Model\PictureRepository::TABLE_NAME, 'id', 'pic', $id, 'gallery_id');
            } else {
                $this->sortHelper->down(Gallery\Model\PictureRepository::TABLE_NAME, 'id', 'pic', $id, 'gallery_id');
            }

            $galleryId = $this->pictureRepository->getGalleryIdFromPictureId($id);

            $this->galleryCache->saveCache($galleryId);

            return $this->redirect()->temporary('acp/gallery/index/edit/id_' . $galleryId);
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _createPost(array $formData, array $settings, $id)
    {
        return $this->actionHelper->handleCreatePostAction(
            function () use ($formData, $settings, $id) {
                $file = $this->request->getFiles()->get('file');

                $this->pictureFormValidation
                    ->setFileRequired(true)
                    ->validate($file);

                $upload = new Core\Helpers\Upload($this->appPath, 'gallery');
                $result = $upload->moveFile($file['tmp_name'], $file['name']);
                $picNum = $this->pictureRepository->getLastPictureByGalleryId($id);

                $insertValues = [
                    'id' => '',
                    'pic' => !is_null($picNum) ? $picNum + 1 : 1,
                    'gallery_id' => $id,
                    'file' => $result['name'],
                    'description' => Core\Functions::strEncode($formData['description'], true),
                    'comments' => $settings['comments'] == 1 ? (isset($formData['comments']) && $formData['comments'] == 1 ? 1 : 0) : $settings['comments'],
                ];

                $lastId = $this->pictureRepository->insert($insertValues);
                $bool2 = $this->galleryHelpers->generatePictureAlias($lastId);

                $this->galleryCache->saveCache($id);

                $this->formTokenHelper->unsetFormToken();

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
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _editPost(array $formData, array $settings, array $picture, $id)
    {
        return $this->actionHelper->handleEditPostAction(
            function () use ($formData, $settings, $picture, $id) {
                $file = $this->request->getFiles()->get('file');

                $this->pictureFormValidation
                    ->setFileRequired(false)
                    ->validate($file);

                $updateValues = [
                    'description' => Core\Functions::strEncode($formData['description'], true),
                    'comments' => $settings['comments'] == 1 ? (isset($formData['comments']) && $formData['comments'] == 1 ? 1 : 0) : $settings['comments'],
                ];

                if (!empty($file)) {
                    $upload = new Core\Helpers\Upload($this->appPath, 'gallery');
                    $result = $upload->moveFile($file['tmp_name'], $file['name']);
                    $oldFile = $this->pictureRepository->getFileById($id);

                    $this->galleryHelpers->removePicture($oldFile);

                    $updateValues = array_merge($updateValues, ['file' => $result['name']]);
                }

                $bool = $this->pictureRepository->update($updateValues, $id);

                $this->galleryCache->saveCache($picture['gallery_id']);

                $this->formTokenHelper->unsetFormToken();

                return $bool;
            },
            'acp/gallery/index/edit/id_' . $picture['gallery_id']
        );
    }
}
