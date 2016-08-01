<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files;
use ACP3\Modules\ACP3\Files\Helpers;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Files\Controller\Admin\Index
 */
class Edit extends AbstractFormAction
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository
     */
    protected $filesRepository;
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
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository $filesRepository
     * @param Files\Model\FilesModel $filesModel
     * @param \ACP3\Modules\ACP3\Files\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Categories\Helpers $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Date $date,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Files\Model\Repository\FilesRepository $filesRepository,
        Files\Model\FilesModel $filesModel,
        Files\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context, $formsHelper, $categoriesHelpers);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->filesRepository = $filesRepository;
        $this->adminFormValidation = $adminFormValidation;
        $this->filesModel = $filesModel;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $file = $this->filesRepository->getOneById($id);

        if (empty($file) === false) {
            $settings = $this->config->getSettings(Files\Installer\Schema::MODULE_NAME);

            $this->title->setPageTitlePostfix($file['title']);

            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $settings, $file, $id);
            }

            $file['filesize'] = substr($file['size'], 0, strpos($file['size'], ' '));

            return [
                'options' => $this->getOptions($settings, $file),
                'units' => $this->formsHelper->choicesGenerator('units', $this->getUnits(),
                    trim(strrchr($file['size'], ' '))),
                'categories' => $this->categoriesHelpers->categoriesList(
                    Files\Installer\Schema::MODULE_NAME,
                    $file['category_id'],
                    true
                ),
                'checked_external' => $this->request->getPost()->has('external') ? ' checked="checked"' : '',
                'current_file' => $file['file'],
                'SEO_FORM_FIELDS' => $this->metaFormFieldsHelper
                    ? $this->metaFormFieldsHelper->formFields(sprintf(Files\Helpers::URL_KEY_PATTERN, $id))
                    : [],
                'form' => array_merge($file, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param array $dl
     * @param int $fileId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $settings, array $dl, $fileId)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $settings, $dl, $fileId) {
            $file = null;
            if (isset($formData['external'])) {
                $file = $formData['file_external'];
            } elseif ($this->request->getFiles()->has('file_internal')) {
                $file = $this->request->getFiles()->get('file_internal');
            }

            $this->adminFormValidation
                ->setFile($file)
                ->setUriAlias(sprintf(Helpers::URL_KEY_PATTERN, $fileId))
                ->validate($formData);

            $formData['cat'] = $this->fetchCategoryId($formData);
            $formData['comments'] = $this->useComments($formData, $settings);

            if (!empty($file)) {
                $newFileSql = $this->updateAssociatedFile($file, $formData, $dl['file']);

                $formData = array_merge($formData, $newFileSql);
            }

            $bool = $this->filesModel->saveFile($formData, $this->user->getUserId(), $fileId);

            $this->insertUriAlias($formData, $fileId);

            return $bool;
        });
    }

    /**
     * @param string|UploadedFile $file
     * @param array $formData
     * @param string $currentFileName
     *
     * @return array
     */
    protected function updateAssociatedFile($file, array $formData, $currentFileName)
    {
        $upload = new Core\Helpers\Upload($this->appPath, 'files');

        if ($file instanceof UploadedFile) {
            $result = $upload->moveFile($file->getPathname(), $file->getClientOriginalName());
            $newFile = $result['name'];
            $fileSize = $result['size'];
        } else {
            $formData['filesize'] = (float)$formData['filesize'];
            $newFile = $file;
            $fileSize = $formData['filesize'] . ' ' . $formData['unit'];
        }

        $upload->removeUploadedFile($currentFileName);

        return [
            'file' => $newFile,
            'filesize' => $fileSize,
        ];
    }
}
