<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files;
use ACP3\Modules\ACP3\Files\Helpers;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Manage extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Files\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Helpers
     */
    protected $commentsHelpers;
    /**
     * @var Files\Model\FilesModel
     */
    protected $filesModel;
    /**
     * @var Core\View\Block\RepositoryAwareFormBlockInterface
     */
    private $block;
    /**
     * @var Categories\Helpers
     */
    private $categoriesHelpers;

    /**
     * Manage constructor.
     *
     * @param Core\Controller\Context\FrontendContext           $context
     * @param Core\View\Block\RepositoryAwareFormBlockInterface $block
     * @param Files\Model\FilesModel                            $filesModel
     * @param Files\Validation\AdminFormValidation              $adminFormValidation
     * @param Categories\Helpers                                $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\RepositoryAwareFormBlockInterface $block,
        Files\Model\FilesModel $filesModel,
        Files\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->filesModel = $filesModel;
        $this->block = $block;
        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @param int|null $id
     *
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function execute(?int $id)
    {
        return $this->block
            ->setDataById($id)
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @param int|null $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost(?int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();
            $file = null;
            if (isset($formData['external'])) {
                $file = $formData['file_external'];
            } elseif ($this->request->getFiles()->has('file_internal')) {
                $file = $this->request->getFiles()->get('file_internal');
            }

            if ($id !== null) {
                $this->adminFormValidation->setUriAlias(\sprintf(Helpers::URL_KEY_PATTERN, $id));
            }

            $this->adminFormValidation
                ->setFile($file)
                ->validate($formData);

            $formData['cat'] = $this->fetchCategoryId($formData);
            $formData['comments'] = $this->useComments($formData);
            $formData['user_id'] = $this->user->getUserId();

            if (!empty($file)) {
                if ($id !== null) {
                    $dl = $this->filesModel->getOneById($id);
                    $formData = \array_merge(
                        $formData,
                        $this->updateAssociatedFile($file, $formData, $dl['file'])
                    );
                } else {
                    $formData = \array_merge(
                        $formData,
                        $this->updateAssociatedFile($file, $formData, null)
                    );
                }
            }

            return $this->filesModel->save($formData, $id);
        });
    }

    /**
     * @param array $formData
     *
     * @return int
     */
    private function fetchCategoryId(array $formData)
    {
        return !empty($formData['cat_create'])
            ? $this->categoriesHelpers->categoryCreate($formData['cat_create'], Files\Installer\Schema::MODULE_NAME)
            : $formData['cat'];
    }

    /**
     * @param array $formData
     *
     * @return int
     */
    private function useComments(array $formData)
    {
        $settings = $this->config->getSettings(Files\Installer\Schema::MODULE_NAME);

        return $settings['comments'] == 1 && isset($formData['comments']) ? 1 : 0;
    }

    /**
     * @param UploadedFile|string|null $file
     * @param array                    $formData
     * @param null|string              $currentFileName
     *
     * @return array
     *
     * @throws Core\Validation\Exceptions\ValidationFailedException
     */
    private function updateAssociatedFile($file, array $formData, ?string $currentFileName)
    {
        $upload = new Core\Helpers\Upload($this->appPath, Files\Installer\Schema::MODULE_NAME);

        if ($file instanceof UploadedFile) {
            $result = $upload->moveFile($file->getPathname(), $file->getClientOriginalName());
            $fileName = $result['name'];
            $fileSize = $result['size'];
        } else {
            $fileName = $file;
            $fileSize = ((float) $formData['filesize']) . ' ' . $formData['unit'];
        }

        if (!empty($currentFileName)) {
            $upload->removeUploadedFile($currentFileName);
        }

        return [
            'file' => $fileName,
            'filesize' => $fileSize,
        ];
    }
}
