<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Create extends AbstractFormAction
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
     * @var \ACP3\Core\Helpers\Upload
     */
    private $filesUploadHelper;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Core\Date $date,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Files\Model\FilesModel $filesModel,
        Files\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\Upload $filesUploadHelper,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context, $formsHelper, $categoriesHelpers);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->adminFormValidation = $adminFormValidation;
        $this->filesModel = $filesModel;
        $this->filesUploadHelper = $filesUploadHelper;
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute()
    {
        $defaults = [
            'title' => '',
            'file_internal' => '',
            'file_external' => '',
            'filesize' => '',
            'text' => '',
            'start' => '',
            'end' => '',
        ];

        $external = [
            1 => $this->translator->t('files', 'external_resource'),
        ];

        return [
            'active' => $this->formsHelper->yesNoCheckboxGenerator('active', 1),
            'options' => $this->getOptions(['comments' => '0']),
            'units' => $this->formsHelper->choicesGenerator('units', $this->getUnits(), ''),
            'categories' => $this->categoriesHelpers->categoriesList(
                Files\Installer\Schema::MODULE_NAME,
                null,
                true
            ),
            'external' => $this->formsHelper->checkboxGenerator('external', $external),
            'form' => \array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
            'SEO_URI_PATTERN' => Files\Helpers::URL_KEY_PATTERN,
            'SEO_ROUTE_NAME' => '',
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost()
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();
            if (isset($formData['external'])) {
                $file = $formData['file_external'];
            } else {
                $file = $this->request->getFiles()->get('file_internal');
            }

            $this->adminFormValidation
                ->setFile($file)
                ->validate($formData);

            if ($file instanceof UploadedFile) {
                $result = $this->filesUploadHelper->moveFile($file->getPathname(), $file->getClientOriginalName());
                $formData['file'] = $result['name'];
                $formData['filesize'] = $result['size'];
            } else {
                $formData['file'] = $file;
                $formData['filesize'] = ((float) $formData['filesize']) . ' ' . $formData['unit'];
            }

            $formData['cat'] = $this->fetchCategoryId($formData);
            $formData['comments'] = $this->useComments($formData);
            $formData['user_id'] = $this->user->getUserId();

            return $this->filesModel->save($formData);
        });
    }
}
