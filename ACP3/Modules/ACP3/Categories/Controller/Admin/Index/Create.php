<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\Categories\Controller\Admin\Index
 */
class Create extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository
     */
    protected $categoryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Cache
     */
    protected $categoriesCache;
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
     * @param \ACP3\Core\Controller\Context\AdminContext                   $context
     * @param \ACP3\Core\Helpers\Forms                                     $formsHelper
     * @param \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository       $categoryRepository
     * @param \ACP3\Modules\ACP3\Categories\Cache                          $categoriesCache
     * @param \ACP3\Modules\ACP3\Categories\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Core\Helpers\FormToken                                 $formTokenHelper
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelper,
        Categories\Model\Repository\CategoryRepository $categoryRepository,
        Categories\Cache $categoriesCache,
        Categories\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->categoryRepository = $categoryRepository;
        $this->categoriesCache = $categoriesCache;
        $this->adminFormValidation = $adminFormValidation;
        $this->formTokenHelper = $formTokenHelper;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->count() !== 0) {
            return $this->executePost($this->request->getPost()->all());
        }

        $modules = $this->modules->getActiveModules();
        foreach ($modules as $name => $info) {
            if ($info['active'] && in_array('categories', $info['dependencies']) === true) {
                $modules[$name]['selected'] = $this->formsHelper->selectEntry('module', $info['id']);
            } else {
                unset($modules[$name]);
            }
        }

        return [
            'form' => array_merge(['title' => '', 'description' => ''], $this->request->getPost()->all()),
            'mod_list' => $modules,
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData) {
            $file = $this->request->getFiles()->get('picture');

            $this->adminFormValidation
                ->setFile($file)
                ->setSettings($this->config->getSettings('categories'))
                ->validate($formData);

            $insertValues = [
                'id' => '',
                'title' => $this->get('core.helpers.secure')->strEncode($formData['title']),
                'description' => $this->get('core.helpers.secure')->strEncode($formData['description']),
                'module_id' => (int)$formData['module'],
            ];
            if (!empty($file)) {
                $upload = new Core\Helpers\Upload($this->appPath, 'categories');
                $result = $upload->moveFile($file['tmp_name'], $file['name']);
                $insertValues['picture'] = $result['name'];
            }

            $bool = $this->categoryRepository->insert($insertValues);

            $this->categoriesCache->saveCache(strtolower($formData['module']));

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $bool;
        });
    }
}
