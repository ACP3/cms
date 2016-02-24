<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures
 */
class Create extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
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
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext                  $context
     * @param \ACP3\Core\Helpers\FormToken                                $formTokenHelper
     * @param \ACP3\Modules\ACP3\Gallery\Helpers                          $galleryHelpers
     * @param \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository          $galleryRepository
     * @param \ACP3\Modules\ACP3\Gallery\Model\PictureRepository          $pictureRepository
     * @param \ACP3\Modules\ACP3\Gallery\Cache                            $galleryCache
     * @param \ACP3\Modules\ACP3\Gallery\Validation\PictureFormValidation $pictureFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Gallery\Helpers $galleryHelpers,
        Gallery\Model\GalleryRepository $galleryRepository,
        Gallery\Model\PictureRepository $pictureRepository,
        Gallery\Cache $galleryCache,
        Gallery\Validation\PictureFormValidation $pictureFormValidation
    )
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
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
    public function execute($id)
    {
        if ($this->galleryRepository->galleryExists($id) === true) {
            $gallery = $this->galleryRepository->getGalleryTitle($id);

            $this->breadcrumb
                ->append($gallery, 'acp/gallery/index/edit/id_' . $id)
                ->append($this->translator->t('gallery', 'admin_pictures_create'));

            $settings = $this->config->getSettings('gallery');

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->executePost($this->request->getPost()->all(), $settings, $id);
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $options = [];
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = $this->get('core.helpers.forms')->selectEntry('comments', '1', '0', 'checked');
                $options[0]['lang'] = $this->translator->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            return [
                'form' => array_merge(['description' => ''], $this->request->getPost()->all()),
                'gallery_id' => $id,
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
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
    protected function executePost(array $formData, array $settings, $id)
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
}
