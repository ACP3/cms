<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @var Gallery\Model\PictureModel
     */
    protected $pictureModel;
    /**
     * @var \ACP3\Core\Helpers\Upload
     */
    private $galleryUploadHelper;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext               $context
     * @param \ACP3\Core\Helpers\Forms                                    $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                                $formTokenHelper
     * @param \ACP3\Modules\ACP3\Gallery\Helpers                          $galleryHelpers
     * @param Gallery\Model\PictureModel                                  $pictureModel
     * @param \ACP3\Modules\ACP3\Gallery\Validation\PictureFormValidation $pictureFormValidation
     * @param \ACP3\Core\Helpers\Upload                                   $galleryUploadHelper
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Gallery\Helpers $galleryHelpers,
        Gallery\Model\PictureModel $pictureModel,
        Gallery\Validation\PictureFormValidation $pictureFormValidation,
        Core\Helpers\Upload $galleryUploadHelper
    ) {
        parent::__construct($context, $formsHelper);

        $this->formTokenHelper = $formTokenHelper;
        $this->galleryHelpers = $galleryHelpers;
        $this->pictureFormValidation = $pictureFormValidation;
        $this->pictureModel = $pictureModel;
        $this->galleryUploadHelper = $galleryUploadHelper;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id)
    {
        $picture = $this->pictureModel->getOneById($id);

        if (!empty($picture)) {
            $this->breadcrumb
                ->append($picture['gallery_title'], 'acp/gallery/pictures/index/id_' . $picture['gallery_id'])
                ->append($this->translator->t('gallery', 'admin_pictures_edit'));

            $this->title
                ->setPageTitlePrefix(
                    $picture['gallery_title']
                    . $this->title->getPageTitleSeparator()
                    . $this->translator->t('gallery', 'picture_x', ['%picture%' => $picture['pic']])
                );

            if ($this->canUseComments() === true) {
                $this->view->assign('options', $this->getOptions($picture['comments']));
            }

            return [
                'form' => \array_merge($picture, $this->request->getPost()->all()),
                'gallery_id' => $picture['gallery_id'],
                'form_token' => $this->formTokenHelper->renderFormToken(),
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost(int $id)
    {
        $picture = $this->pictureModel->getOneById($id);

        return $this->actionHelper->handleSaveAction(
            function () use ($picture, $id) {
                $formData = $this->request->getPost()->all();
                /** @var UploadedFile $file */
                $file = $this->request->getFiles()->get('file');

                $this->pictureFormValidation
                    ->setFileRequired(false)
                    ->setFile($file)
                    ->validate([]);

                if (!empty($file)) {
                    $result = $this->galleryUploadHelper->moveFile($file->getPathname(), $file->getClientOriginalName());

                    $this->galleryHelpers->removePicture($picture['file']);

                    $formData['file'] = $result['name'];
                }

                $formData['gallery_id'] = $picture['gallery_id'];

                return $this->pictureModel->save($formData, $id);
            },
            'acp/gallery/pictures/index/id_' . $picture['gallery_id']
        );
    }
}
