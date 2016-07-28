<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\Seo\Core\Router\Aliases;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures
 */
class Create extends AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository
     */
    protected $galleryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Cache
     */
    protected $galleryCache;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Validation\PictureFormValidation
     */
    protected $pictureFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    protected $pictureRepository;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Core\Router\Aliases
     */
    protected $aliases;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatements;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    protected $uriAliasManager;
    /**
     * @var Gallery\Model\PictureModel
     */
    protected $pictureModel;

    /**
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository $galleryRepository
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository $pictureRepository
     * @param Gallery\Model\PictureModel $pictureModel
     * @param \ACP3\Modules\ACP3\Gallery\Cache $galleryCache
     * @param \ACP3\Modules\ACP3\Gallery\Validation\PictureFormValidation $pictureFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Gallery\Model\Repository\GalleryRepository $galleryRepository,
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        Gallery\Model\PictureModel $pictureModel,
        Gallery\Cache $galleryCache,
        Gallery\Validation\PictureFormValidation $pictureFormValidation
    ) {
        parent::__construct($context, $formsHelper);

        $this->formTokenHelper = $formTokenHelper;
        $this->galleryRepository = $galleryRepository;
        $this->pictureRepository = $pictureRepository;
        $this->galleryCache = $galleryCache;
        $this->pictureFormValidation = $pictureFormValidation;
        $this->pictureModel = $pictureModel;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Core\Router\Aliases $aliases
     */
    public function setAliases(Aliases $aliases)
    {
        $this->aliases = $aliases;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements $metaStatements
     */
    public function setMetaStatements(MetaStatements $metaStatements)
    {
        $this->metaStatements = $metaStatements;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager $uriAliasManager
     */
    public function setUriAliasManager(UriAliasManager $uriAliasManager)
    {
        $this->uriAliasManager = $uriAliasManager;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        if ($this->galleryRepository->galleryExists($id) === true) {
            $gallery = $this->galleryRepository->getGalleryTitle($id);

            $this->breadcrumb
                ->append($gallery, 'acp/gallery/index/edit/id_' . $id)
                ->append($this->translator->t('gallery', 'admin_pictures_create'));

            $settings = $this->config->getSettings(Gallery\Installer\Schema::MODULE_NAME);

            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $settings, $id);
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $this->view->assign('options', $this->getOptions('0'));
            }

            return [
                'form' => array_merge(['description' => ''], $this->request->getPost()->all()),
                'gallery_id' => $id,
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param int   $galleryId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $settings, $galleryId)
    {
        return $this->actionHelper->handleCreatePostAction(
            function () use ($formData, $settings, $galleryId) {
                /** @var UploadedFile $file */
                $file = $this->request->getFiles()->get('file');

                $this->pictureFormValidation
                    ->setFileRequired(true)
                    ->setFile($file)
                    ->validate([]);

                $upload = new Core\Helpers\Upload($this->appPath, 'gallery');
                $result = $upload->moveFile($file->getPathname(), $file->getClientOriginalName());

                $formData['file'] = $result['name'];
                $lastId = $this->pictureModel->savePicture($formData, $galleryId);

                $bool2 = $this->generatePictureAlias($lastId);

                $this->galleryCache->saveCache($galleryId);

                return $lastId && $bool2;
            },
            'acp/gallery/index/edit/id_' . $galleryId
        );
    }

    /**
     * Setzt einen einzelnen Alias für ein Bild einer Fotogalerie
     *
     * @param integer $pictureId
     *
     * @return boolean
     */
    protected function generatePictureAlias($pictureId)
    {
        if ($this->aliases && $this->metaStatements && $this->uriAliasManager) {
            $galleryId = $this->pictureRepository->getGalleryIdFromPictureId($pictureId);
            $alias = $this->aliases->getUriAlias(sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $galleryId), true);
            if (!empty($alias)) {
                $alias .= '/img-' . $pictureId;
            }
            $seoKeywords = $this->metaStatements->getKeywords(sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY,
                $galleryId));
            $seoDescription = $this->metaStatements->getDescription(sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY,
                $galleryId));

            return $this->uriAliasManager->insertUriAlias(
                sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $pictureId),
                $alias,
                $seoKeywords,
                $seoDescription
            );
        }

        return true;
    }
}
