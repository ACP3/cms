<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\View\Block\Frontend;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Gallery\Helpers;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryPicturesRepository;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

class GalleryPictureDetailsBlock extends AbstractBlock
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var GalleryPicturesRepository
     */
    private $pictureRepository;
    /**
     * @var MetaStatements
     */
    private $metaStatements;
    /**
     * @var ApplicationPath
     */
    private $appPath;

    /**
     * GalleryPictureDetailsBlock constructor.
     * @param BlockContext $context
     * @param ApplicationPath $appPath
     * @param RouterInterface $router
     * @param SettingsInterface $settings
     * @param GalleryPicturesRepository $pictureRepository
     */
    public function __construct(
        BlockContext $context,
        ApplicationPath $appPath,
        RouterInterface $router,
        SettingsInterface $settings,
        GalleryPicturesRepository $pictureRepository
    ) {
        parent::__construct($context);

        $this->router = $router;
        $this->settings = $settings;
        $this->pictureRepository = $pictureRepository;
        $this->appPath = $appPath;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements $metaStatements
     */
    public function setMetaStatements(MetaStatements $metaStatements)
    {
        $this->metaStatements = $metaStatements;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        $picture = $this->pictureRepository->getOneById($data['picture_id']);

        $this->breadcrumb
            ->append($this->translator->t('gallery', 'gallery'), 'gallery')
            ->append($picture['title'], 'gallery/index/pics/id_' . $picture['gallery_id'])
            ->append($this->translator->t('gallery', 'picture_x', ['%picture%' => $picture['pic']]));

        $this->title->setPageTitlePrefix($picture['title']);

        $picture = $this->calculatePictureDimensions($picture);

        $previousPicture = $this->pictureRepository->getPreviousPictureId($picture['pic'], $picture['gallery_id']);
        $this->setPreviousPage($previousPicture);

        $nextPicture = $this->pictureRepository->getNextPictureId($picture['pic'], $picture['gallery_id']);
        $this->setNextPage($nextPicture);

        return [
            'picture' => $picture,
            'picture_next' => $nextPicture,
            'picture_previous' => $previousPicture,
            'comments_allowed' => $this->isCommentsAllowed((int)$picture['comments']),
        ];
    }

    /**
     * @param array $picture
     *
     * @return array
     */
    private function calculatePictureDimensions(array $picture): array
    {
        $settings = $this->getGallerySettings();

        $picture['width'] = $settings['width'];
        $picture['height'] = $settings['height'];
        $picInfos = @\getimagesize($this->appPath->getUploadsDir() . 'gallery/' . $picture['file']);
        if ($picInfos !== false) {
            if ($picInfos[0] > $settings['width'] || $picInfos[1] > $settings['height']) {
                if ($picInfos[0] > $picInfos[1]) {
                    $newWidth = $settings['width'];
                    $newHeight = (int)($picInfos[1] * $newWidth / $picInfos[0]);
                } else {
                    $newHeight = $settings['height'];
                    $newWidth = (int)($picInfos[0] * $newHeight / $picInfos[1]);
                }
            }

            $picture['width'] = $newWidth ?? $picInfos[0];
            $picture['height'] = $newHeight ?? $picInfos[1];
        }

        return $picture;
    }

    /**
     * @return array
     */
    private function getGallerySettings(): array
    {
        return $this->settings->getSettings(Schema::MODULE_NAME);
    }

    /**
     * @param int $nextPicture
     */
    private function setNextPage(int $nextPicture)
    {
        if ($this->metaStatements && !empty($nextPicture)) {
            $this->metaStatements->setNextPage(
                $this->router->route(\sprintf(Helpers::URL_KEY_PATTERN_PICTURE, $nextPicture))
            );
        }
    }

    /**
     * @param int $previousPicture
     */
    private function setPreviousPage(int $previousPicture)
    {
        if ($this->metaStatements && !empty($previousPicture)) {
            $this->metaStatements->setPreviousPage(
                $this->router->route(\sprintf(Helpers::URL_KEY_PATTERN_PICTURE, $previousPicture))
            );
        }
    }

    /**
     * @param int $pictureCommentsAllowed
     *
     * @return bool
     */
    private function isCommentsAllowed(int $pictureCommentsAllowed): bool
    {
        $settings = $this->getGallerySettings();

        return $settings['overlay'] == 0 && $settings['comments'] == 1 && $pictureCommentsAllowed == 1;
    }
}
