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
use ACP3\Core\Router\RouterInterface;
use ACP3\Modules\ACP3\Gallery\Repository\GalleryRepository;
use ACP3\Modules\ACP3\System\ViewProviders\BackUriTrait;

class AdminGalleryPictureCreateViewProvider
{
    use BackUriTrait;

    public function __construct(
        private readonly FormToken $formTokenHelper,
        private readonly GalleryRepository $galleryRepository,
        private readonly RequestInterface $request,
        private readonly RouterInterface $router,
        private readonly Steps $breadcrumb,
        private readonly Translator $translator,
    ) {
    }

    /**
     * @return array<string, mixed>
     *
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
            'BACK_URI' => $this->getBackUri($this->router->route("acp/gallery/index/edit/id_{$galleryId}", true)),
        ];
    }
}
