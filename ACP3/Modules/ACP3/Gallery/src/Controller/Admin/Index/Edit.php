<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Modules\ACP3\Gallery;

class Edit extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Validation\GalleryFormValidation
     */
    private $galleryFormValidation;
    /**
     * @var Gallery\Model\GalleryModel
     */
    private $galleryModel;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\ViewProviders\AdminGalleryEditViewProvider
     */
    private $adminGalleryEditViewProvider;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        UserModelInterface $user,
        Gallery\Model\GalleryModel $galleryModel,
        Gallery\Validation\GalleryFormValidation $galleryFormValidation,
        Gallery\ViewProviders\AdminGalleryEditViewProvider $adminGalleryEditViewProvider
    ) {
        parent::__construct($context);

        $this->galleryModel = $galleryModel;
        $this->galleryFormValidation = $galleryFormValidation;
        $this->adminGalleryEditViewProvider = $adminGalleryEditViewProvider;
        $this->user = $user;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id): array
    {
        $gallery = $this->galleryModel->getOneById($id);

        if (!empty($gallery)) {
            return ($this->adminGalleryEditViewProvider)($gallery);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost(int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->galleryFormValidation
                ->setUriAlias(\sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $id))
                ->validate($formData);

            $formData['user_id'] = $this->user->getUserId();

            return $this->galleryModel->save($formData, $id);
        });
    }
}
