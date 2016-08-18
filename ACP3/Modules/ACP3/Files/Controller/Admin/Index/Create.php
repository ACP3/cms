<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Files\Controller\Admin\Index
 */
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
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param Files\Model\FilesModel $filesModel
     * @param \ACP3\Modules\ACP3\Files\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Categories\Helpers $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Date $date,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Files\Model\FilesModel $filesModel,
        Files\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers)
    {
        parent::__construct($context, $formsHelper, $categoriesHelpers);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->adminFormValidation = $adminFormValidation;
        $this->filesModel = $filesModel;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        $settings = $this->config->getSettings(Files\Installer\Schema::MODULE_NAME);

        if ($this->request->getPost()->count() !== 0) {
            return $this->executePost($this->request->getPost()->all(), $settings);
        }

        $defaults = [
            'title' => '',
            'file_internal' => '',
            'file_external' => '',
            'filesize' => '',
            'text' => '',
            'start' => '',
            'end' => ''
        ];

        return [
            'options' => $this->getOptions($settings, ['comments' => '0']),
            'units' => $this->formsHelper->choicesGenerator('units', $this->getUnits(), ''),
            'categories' => $this->categoriesHelpers->categoriesList(Files\Installer\Schema::MODULE_NAME, '', true),
            'checked_external' => $this->request->getPost()->has('external') ? ' checked="checked"' : '',
            'SEO_FORM_FIELDS' => $this->metaFormFieldsHelper ? $this->metaFormFieldsHelper->formFields() : [],
            'form' => array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $settings)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData, $settings) {
            if (isset($formData['external'])) {
                $file = $formData['file_external'];
            } else {
                $file = $this->request->getFiles()->get('file_internal');
            }

            $this->adminFormValidation
                ->setFile($file)
                ->validate($formData);

            if (is_array($file) === true) {
                $upload = new Core\Helpers\Upload($this->appPath, Files\Installer\Schema::MODULE_NAME);
                $result = $upload->moveFile($file->getPathname(), $file->getClientOriginalName());
                $formData['file'] = $result['name'];
                $formData['filesize'] = $result['size'];
            } else {
                $formData['file'] = $file;
                $formData['filesize'] = ((float)$formData['filesize']) . ' ' . $formData['unit'];
            }

            $formData['cat'] = $this->fetchCategoryId($formData);
            $formData['comments'] = $this->useComments($formData, $settings);

            $lastId = $this->filesModel->saveFile($formData, $this->user->getUserId());

            $this->insertUriAlias($formData, $lastId);

            return $lastId;
        });
    }
}
