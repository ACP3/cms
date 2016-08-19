<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @var \ACP3\Modules\ACP3\Gallery\Validation\PictureFormValidation
     */
    protected $pictureFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    protected $pictureRepository;
    /**
     * @var Gallery\Model\PictureModel
     */
    protected $pictureModel;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Modules\ACP3\Gallery\Helpers $galleryHelpers
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository $pictureRepository
     * @param Gallery\Model\PictureModel $pictureModel
     * @param \ACP3\Modules\ACP3\Gallery\Validation\PictureFormValidation $pictureFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Gallery\Helpers $galleryHelpers,
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        Gallery\Model\PictureModel $pictureModel,
        Gallery\Validation\PictureFormValidation $pictureFormValidation
    ) {
        parent::__construct($context, $formsHelper);

        $this->formTokenHelper = $formTokenHelper;
        $this->galleryHelpers = $galleryHelpers;
        $this->pictureRepository = $pictureRepository;
        $this->pictureFormValidation = $pictureFormValidation;
        $this->pictureModel = $pictureModel;
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
            $picture = $this->pictureRepository->getOneById($id);

            $this->breadcrumb
                ->append($picture['title'], 'acp/gallery/index/edit/id_' . $picture['gallery_id'])
                ->append($this->translator->t('gallery', 'admin_pictures_edit'));

            $this->title
                ->setPageTitlePostfix(
                    $picture['title']
                    . $this->title->getPageTitleSeparator()
                    . $this->translator->t('gallery', 'picture_x', ['%picture%' => $picture['pic']])
                );

            $settings = $this->config->getSettings(Gallery\Installer\Schema::MODULE_NAME);

            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $settings, $picture, $id);
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $this->view->assign('options', $this->getOptions($picture['comments']));
            }

            return [
                'form' => array_merge($picture, $this->request->getPost()->all()),
                'gallery_id' => $picture['gallery_id'],
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param array $picture
     * @param int   $pictureId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $settings, array $picture, $pictureId)
    {
        return $this->actionHelper->handleEditPostAction(
            function () use ($formData, $settings, $picture, $pictureId) {
                /** @var UploadedFile $file */
                $file = $this->request->getFiles()->get('file');

                $this->pictureFormValidation
                    ->setFileRequired(false)
                    ->setFile($file)
                    ->validate([]);

                if (!empty($file)) {
                    $upload = new Core\Helpers\Upload($this->appPath, Gallery\Installer\Schema::MODULE_NAME);
                    $result = $upload->moveFile($file->getPathname(), $file->getClientOriginalName());
                    $oldFile = $this->pictureRepository->getFileById($pictureId);

                    $this->galleryHelpers->removePicture($oldFile);

                    $formData['file'] = $result['name'];
                }

                return $this->pictureModel->savePicture($formData, $picture['gallery_id'], $pictureId);
            },
            'acp/gallery/index/edit/id_' . $picture['gallery_id']
        );
    }
}
