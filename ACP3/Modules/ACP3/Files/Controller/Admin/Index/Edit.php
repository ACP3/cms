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
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param Files\Model\FilesModel $filesModel
     * @param \ACP3\Modules\ACP3\Files\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Categories\Helpers $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Files\Model\FilesModel $filesModel,
        Files\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context, $formsHelper, $categoriesHelpers);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->adminFormValidation = $adminFormValidation;
        $this->filesModel = $filesModel;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $file = $this->filesModel->getOneById($id);

        if (empty($file) === false) {
            $this->title->setPageTitlePostfix($file['title']);

            $file['filesize'] = '';
            $file['file_external'] = '';

            $external = [
                1 => $this->translator->t('files', 'external_resource')
            ];

            return [
                'options' => $this->getOptions($file),
                'units' => $this->formsHelper->choicesGenerator('units', $this->getUnits(),
                    trim(strrchr($file['size'], ' '))),
                'categories' => $this->categoriesHelpers->categoriesList(
                    Files\Installer\Schema::MODULE_NAME,
                    $file['category_id'],
                    true
                ),
                'external' => $this->formsHelper->checkboxGenerator('external', $external),
                'current_file' => $file['file'],
                'form' => array_merge($file, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken(),
                'SEO_URI_PATTERN' => Files\Helpers::URL_KEY_PATTERN,
                'SEO_ROUTE_NAME' => sprintf(Files\Helpers::URL_KEY_PATTERN, $id)
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost($id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();
            $file = null;
            if (isset($formData['external'])) {
                $file = $formData['file_external'];
            } elseif ($this->request->getFiles()->has('file_internal')) {
                $file = $this->request->getFiles()->get('file_internal');
            }
            $dl = $this->filesModel->getOneById($id);

            $this->adminFormValidation
                ->setFile($file)
                ->setUriAlias(sprintf(Helpers::URL_KEY_PATTERN, $id))
                ->validate($formData);

            $formData['cat'] = $this->fetchCategoryId($formData);
            $formData['comments'] = $this->useComments($formData);
            $formData['user_id'] = $this->user->getUserId();

            if (!empty($file)) {
                $newFileSql = $this->updateAssociatedFile($file, $formData, $dl['file']);

                $formData = array_merge($formData, $newFileSql);
            }

            return $this->filesModel->save($formData, $id);
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
        $upload = new Core\Helpers\Upload($this->appPath, Files\Installer\Schema::MODULE_NAME);

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
