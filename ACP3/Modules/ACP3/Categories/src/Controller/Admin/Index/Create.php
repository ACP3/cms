<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

class Create extends AbstractFormAction
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
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Modules $modules,
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
        $this->modules = $modules;
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute()
    {
        return [
            'form' => \array_merge($this->getDefaultFormData(), $this->request->getPost()->all()),
            'category_tree' => $this->fetchCategoryTree(),
            'mod_list' => $this->fetchModules(),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    private function getDefaultFormData(): array
    {
        return [
            'parent_id' => 0,
            'title' => '',
            'description' => '',
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
            $file = $this->request->getFiles()->get('picture');

            $this->adminFormValidation
                ->setFile($file)
                ->validate($formData);

            if (!empty($file)) {
                $result = $this->categoriesUploadHelper->moveFile($file->getPathname(), $file->getClientOriginalName());
                $formData['picture'] = $result['name'];
            }

            return $this->categoriesModel->save($formData);
        });
    }

    /**
     * @return array
     */
    protected function fetchModules()
    {
        $modules = $this->modules->getActiveModules();
        foreach ($modules as $name => $info) {
            if ($info['active'] && \in_array('categories', $info['dependencies']) === true) {
                $modules[$name]['selected'] = $this->formsHelper->selectEntry('module_id', $info['id']);
            } else {
                unset($modules[$name]);
            }
        }

        return $modules;
    }
}
