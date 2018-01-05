<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\View\Block\Admin;

use ACP3\Core\Modules\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractRepositoryAwareFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryPicturesRepository;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;

class GalleryPictureAdminFormBlock extends AbstractRepositoryAwareFormBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var GalleryRepository
     */
    private $galleryRepository;

    /**
     * PictureFormBlock constructor.
     * @param FormBlockContext $context
     * @param GalleryPicturesRepository $galleryPicturesRepository
     * @param SettingsInterface $settings
     * @param Modules $modules
     * @param GalleryRepository $galleryRepository
     */
    public function __construct(
        FormBlockContext $context,
        GalleryPicturesRepository $galleryPicturesRepository,
        SettingsInterface $settings,
        Modules $modules,
        GalleryRepository $galleryRepository
    ) {
        parent::__construct($context, $galleryPicturesRepository);

        $this->settings = $settings;
        $this->modules = $modules;
        $this->galleryRepository = $galleryRepository;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        $galleryTitle = $this->galleryRepository->getGalleryTitle($data['gallery_id']);

        $this->breadcrumb
            ->append($galleryTitle, 'acp/gallery/pictures/index/id_' . $data['gallery_id']);

        if (!empty($data['pic'])) {
            $this->breadcrumb
                ->append($this->translator->t('gallery', 'admin_pictures_edit'));

            $this->title->setPageTitlePrefix(
                $galleryTitle
                . $this->title->getPageTitleSeparator()
                . $this->translator->t('gallery', 'picture_x', ['%picture%' => $data['pic']])
            );
        } else {
            $this->breadcrumb
                ->append($this->translator->t('gallery', 'admin_pictures_create'));
        }

        return [
            'form' => \array_merge($data, $this->getRequestData()),
            'gallery_id' => $data['gallery_id'],
            'form_token' => $this->formToken->renderFormToken(),
            'options' => $this->fetchOptions($data),
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    private function fetchOptions(array $data): array
    {
        if ($this->hasAvailableOptions()) {
            $comments = [
                '1' => $this->translator->t('system', 'allow_comments'),
            ];

            return $this->forms->checkboxGenerator('comments', $comments, $data['comments']);
        }

        return [];
    }

    /**
     * @return bool
     */
    protected function hasAvailableOptions(): bool
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        return $settings['overlay'] == 0
            && $settings['comments'] == 1
            && $this->modules->isActive('comments') === true;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [
            'comments' => '0',
            'description' => '',
        ];
    }
}
