<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\View\Block\Admin;


use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;

class PictureFormBlock extends AbstractFormBlock
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
     * @param SettingsInterface $settings
     * @param Modules $modules
     * @param GalleryRepository $galleryRepository
     */
    public function __construct(
        FormBlockContext $context,
        SettingsInterface $settings,
        Modules $modules,
        GalleryRepository $galleryRepository
    ) {
        parent::__construct($context);

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
            ->append($galleryTitle, 'acp/gallery/index/edit/id_' . $data['gallery_id']);

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

        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        if ($this->hasAvailableOptions($settings)) {
            $this->view->assign('options', $this->getOptions($data['comments']));
        }

        return [
            'form' => array_merge($data, $this->getRequestData()),
            'gallery_id' => $data['gallery_id'],
            'form_token' => $this->formToken->renderFormToken()
        ];
    }

    /**
     * @param array $settings
     * @return bool
     */
    protected function hasAvailableOptions(array $settings): bool
    {
        return $settings['overlay'] == 0
            && $settings['comments'] == 1
            && $this->modules->isActive('comments') === true;
    }

    /**
     * @param string $allowComments
     *
     * @return array
     */
    private function getOptions($allowComments = '0'): array
    {
        $comments = [
            '1' => $this->translator->t('system', 'allow_comments')
        ];

        return $this->forms->checkboxGenerator('comments', $comments, $allowComments);
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [
            'comments' => '0',
            'description' => ''
        ];
    }
}
