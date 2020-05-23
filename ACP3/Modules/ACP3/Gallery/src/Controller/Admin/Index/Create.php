<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

class Create extends Core\Controller\AbstractFrontendAction
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

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Gallery\Model\GalleryModel $galleryModel,
        Gallery\Validation\GalleryFormValidation $galleryFormValidation,
        Gallery\ViewProviders\AdminGalleryEditViewProvider $adminGalleryEditViewProvider
    ) {
        parent::__construct($context);

        $this->galleryModel = $galleryModel;
        $this->galleryFormValidation = $galleryFormValidation;
        $this->adminGalleryEditViewProvider = $adminGalleryEditViewProvider;
    }

    public function execute(): array
    {
        $defaults = [
            'id' => null,
            'active' => 1,
            'title' => '',
            'description' => '',
            'start' => '',
            'end' => '',
        ];

        return ($this->adminGalleryEditViewProvider)($defaults);
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost()
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();

            $this->galleryFormValidation->validate($formData);

            $formData['user_id'] = $this->user->getUserId();

            return $this->galleryModel->save($formData);
        });
    }
}
