<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\ViewProviders;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;

class AdminGalleryPictureEditViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $breadcrumb;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository
     */
    private $galleryRepository;

    public function __construct(
        Forms $formsHelper,
        FormToken $formTokenHelper,
        GalleryRepository $galleryRepository,
        RequestInterface $request,
        Steps $breadcrumb,
        Title $title,
        Translator $translator
    ) {
        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->request = $request;
        $this->breadcrumb = $breadcrumb;
        $this->title = $title;
        $this->translator = $translator;
        $this->galleryRepository = $galleryRepository;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(array $picture): array
    {
        $this->breadcrumb
            ->append($picture['gallery_title'], 'acp/gallery/pictures/index/id_' . $picture['gallery_id'])
            ->append(
                $this->translator->t('gallery', 'admin_pictures_edit'),
                $this->request->getQuery()
            );

        $this->title
            ->setPageTitlePrefix(
                $picture['gallery_title']
                . $this->title->getPageTitleSeparator()
                . $this->translator->t('gallery', 'picture_x', ['%picture%' => $picture['pic']])
            );

        return [
            'form' => \array_merge($picture, $this->request->getPost()->all()),
            'galleries' => $this->formsHelper->choicesGenerator(
                'gallery_id',
                $this->getGalleries(),
                $picture['gallery_id']
            ),
            'gallery_id' => $picture['gallery_id'],
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function getGalleries(): array
    {
        $galleries = [];
        foreach ($this->galleryRepository->getAll() as $gallery) {
            $galleries[$gallery['id']] = $gallery['title'];
        }

        return $galleries;
    }
}
