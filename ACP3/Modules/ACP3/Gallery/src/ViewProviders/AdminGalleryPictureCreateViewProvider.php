<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\ViewProviders;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;

class AdminGalleryPictureCreateViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository
     */
    private $galleryRepository;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $breadcrumb;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        FormToken $formTokenHelper,
        GalleryRepository $galleryRepository,
        RequestInterface $request,
        Steps $breadcrumb,
        Translator $translator
    ) {
        $this->formTokenHelper = $formTokenHelper;
        $this->galleryRepository = $galleryRepository;
        $this->request = $request;
        $this->breadcrumb = $breadcrumb;
        $this->translator = $translator;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $galleryId): array
    {
        $gallery = $this->galleryRepository->getGalleryTitle($galleryId);

        $this->breadcrumb
            ->append($gallery, 'acp/gallery/pictures/index/id_' . $galleryId)
            ->append(
                $this->translator->t('gallery', 'admin_pictures_create'),
                $this->request->getQuery()
            );

        return [
            'form' => array_merge(['title' => '', 'description' => ''], $this->request->getPost()->all()),
            'gallery_id' => $galleryId,
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
