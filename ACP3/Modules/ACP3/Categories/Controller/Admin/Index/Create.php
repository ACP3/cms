<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

class Create extends Core\Controller\AbstractFrontendAction
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
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var Categories\Model\CategoriesModel
     */
    protected $categoriesModel;
    /**
     * @var \ACP3\Core\Helpers\Upload
     */
    private $categoriesUploadHelper;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext                $context
     * @param \ACP3\Core\Helpers\Forms                                     $formsHelper
     * @param Categories\Model\CategoriesModel                             $categoriesModel
     * @param \ACP3\Modules\ACP3\Categories\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Core\Helpers\Upload                                    $categoriesUploadHelper
     * @param \ACP3\Core\Helpers\FormToken                                 $formTokenHelper
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Categories\Model\CategoriesModel $categoriesModel,
        Categories\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\Upload $categoriesUploadHelper,
        Core\Helpers\FormToken $formTokenHelper
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->adminFormValidation = $adminFormValidation;
        $this->formTokenHelper = $formTokenHelper;
        $this->categoriesModel = $categoriesModel;
        $this->categoriesUploadHelper = $categoriesUploadHelper;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return [
            'form' => \array_merge(['title' => '', 'description' => ''], $this->request->getPost()->all()),
            'mod_list' => $this->fetchModules(),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();
            $file = $this->request->getFiles()->get('picture');

            $this->adminFormValidation
                ->setFile($file)
                ->setSettings($this->config->getSettings(Categories\Installer\Schema::MODULE_NAME))
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
                $modules[$name]['selected'] = $this->formsHelper->selectEntry('module', $info['id']);
            } else {
                unset($modules[$name]);
            }
        }

        return $modules;
    }
}
