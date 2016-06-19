<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures
 */
class Edit extends AbstractFormAction
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
     * @var \ACP3\Modules\ACP3\Gallery\Cache
     */
    protected $galleryCache;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Validation\PictureFormValidation
     */
    protected $pictureFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    protected $pictureRepository;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext                  $context
     * @param \ACP3\Core\Helpers\Forms                                    $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                                $formTokenHelper
     * @param \ACP3\Modules\ACP3\Gallery\Helpers                          $galleryHelpers
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository          $pictureRepository
     * @param \ACP3\Modules\ACP3\Gallery\Cache                            $galleryCache
     * @param \ACP3\Modules\ACP3\Gallery\Validation\PictureFormValidation $pictureFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Gallery\Helpers $galleryHelpers,
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        Gallery\Cache $galleryCache,
        Gallery\Validation\PictureFormValidation $pictureFormValidation
    ) {
        parent::__construct($context, $formsHelper);

        $this->formTokenHelper = $formTokenHelper;
        $this->galleryHelpers = $galleryHelpers;
        $this->pictureRepository = $pictureRepository;
        $this->galleryCache = $galleryCache;
        $this->pictureFormValidation = $pictureFormValidation;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        if ($this->pictureRepository->pictureExists($id) === true) {
            $picture = $this->pictureRepository->getPictureById($id);

            $this->breadcrumb
                ->append($picture['title'], 'acp/gallery/index/edit/id_' . $picture['gallery_id'])
                ->append($this->translator->t('gallery', 'admin_pictures_edit'));

            $this->title
                ->setPageTitlePostfix(
                    $picture['title']
                    . $this->title->getPageTitleSeparator()
                    . $this->translator->t('gallery', 'picture_x', ['%picture%' => $picture['pic']])
                );

            $settings = $this->config->getSettings('gallery');

            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $settings, $picture, $id);
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $this->view->assign('options', $this->getOptions($picture['comments']));
            }

            return [
                'form' => array_merge($picture, $this->request->getPost()->all()),
                'gallery_id' => $id,
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param array $picture
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $settings, array $picture, $id)
    {
        return $this->actionHelper->handleEditPostAction(
            function () use ($formData, $settings, $picture, $id) {
                $file = $this->request->getFiles()->get('file');

                $this->pictureFormValidation
                    ->setFileRequired(false)
                    ->validate($file);

                $updateValues = [
                    'description' => $this->get('core.helpers.secure')->strEncode($formData['description'], true),
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

                Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

                return $bool;
            },
            'acp/gallery/index/edit/id_' . $picture['gallery_id']
        );
    }
}
