<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Categories\Controller\Admin\Index
 */
class Edit extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var Categories\Model\CategoriesModel
     */
    protected $categoriesModel;

    /**
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Categories\Model\CategoriesModel $categoriesModel
     * @param \ACP3\Modules\ACP3\Categories\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Categories\Model\CategoriesModel $categoriesModel,
        Categories\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->formTokenHelper = $formTokenHelper;
        $this->categoriesModel = $categoriesModel;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $category = $this->categoriesModel->getOneById($id);

        if (empty($category) === false) {
            $this->title->setPageTitlePostfix($category['title']);

            return [
                'form' => array_merge($category, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
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
            $file = $this->request->getFiles()->get('picture');

            $this->adminFormValidation
                ->setFile($file)
                ->setSettings($this->config->getSettings(Categories\Installer\Schema::MODULE_NAME))
                ->setCategoryId($id)
                ->validate($formData);

            if (empty($file) === false) {
                $category = $this->categoriesModel->getOneById($id);
                $upload = new Core\Helpers\Upload($this->appPath, Categories\Installer\Schema::MODULE_NAME);
                $upload->removeUploadedFile($category['picture']);
                $result = $upload->moveFile($file->getPathname(), $file->getClientOriginalName());
                $formData['picture'] = $result['name'];
            }

            return $this->categoriesModel->save($formData, $id);
        });
    }
}
