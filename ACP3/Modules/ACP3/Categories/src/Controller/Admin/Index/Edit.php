<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

class Edit extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Validation\AdminFormValidation
     */
    private $adminFormValidation;
    /**
     * @var Categories\Model\CategoriesModel
     */
    private $categoriesModel;
    /**
     * @var \ACP3\Core\Helpers\Upload
     */
    private $categoriesUploadHelper;
    /**
     * @var \ACP3\Modules\ACP3\Categories\ViewProviders\AdminCategoryEditViewProvider
     */
    private $adminCategoryEditViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Categories\Model\CategoriesModel $categoriesModel,
        Categories\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\Upload $categoriesUploadHelper,
        Categories\ViewProviders\AdminCategoryEditViewProvider $adminCategoryEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->categoriesModel = $categoriesModel;
        $this->categoriesUploadHelper = $categoriesUploadHelper;
        $this->adminCategoryEditViewProvider = $adminCategoryEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id): array
    {
        $category = $this->categoriesModel->getOneById($id);

        if (empty($category) === false) {
            return ($this->adminCategoryEditViewProvider)($category);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost(int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();
            $file = $this->request->getFiles()->get('picture');

            $this->adminFormValidation
                ->setFile($file)
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
