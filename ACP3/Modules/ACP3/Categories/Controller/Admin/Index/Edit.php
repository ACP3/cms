<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

class Edit extends AbstractFormAction
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
     * @var \ACP3\Core\Helpers\Upload
     */
    private $categoriesUploadHelper;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext                     $context
     * @param \ACP3\Core\Helpers\Forms                                          $formsHelper
     * @param Categories\Model\CategoriesModel                                  $categoriesModel
     * @param \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository $categoryRepository
     * @param \ACP3\Modules\ACP3\Categories\Validation\AdminFormValidation      $adminFormValidation
     * @param \ACP3\Core\Helpers\Upload                                         $categoriesUploadHelper
     * @param \ACP3\Core\Helpers\FormToken                                      $formTokenHelper
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Categories\Model\CategoriesModel $categoriesModel,
        Categories\Model\Repository\CategoryRepository $categoryRepository,
        Categories\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\Upload $categoriesUploadHelper,
        Core\Helpers\FormToken $formTokenHelper
    ) {
        parent::__construct($context, $formsHelper, $categoryRepository);

        $this->adminFormValidation = $adminFormValidation;
        $this->formTokenHelper = $formTokenHelper;
        $this->categoriesModel = $categoriesModel;
        $this->categoriesUploadHelper = $categoriesUploadHelper;
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id)
    {
        $category = $this->categoriesModel->getOneById($id);

        if (empty($category) === false) {
            $this->title->setPageTitlePrefix($category['title']);

            return [
                'form' => \array_merge($category, $this->request->getPost()->all()),
                'category_tree' => $this->fetchCategoryTree(
                    $category['module_id'],
                    $category['parent_id'],
                    $category['left_id'],
                    $category['right_id']
                ),
                'form_token' => $this->formTokenHelper->renderFormToken(),
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost(int $id)
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
                $this->categoriesUploadHelper->removeUploadedFile($category['picture']);
                $result = $this->categoriesUploadHelper->moveFile($file->getPathname(), $file->getClientOriginalName());
                $formData['picture'] = $result['name'];
            }

            return $this->categoriesModel->save($formData, $id);
        });
    }
}
