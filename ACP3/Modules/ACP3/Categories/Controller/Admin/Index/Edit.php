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
     * @param \ACP3\Core\Controller\Context\AdminContext                   $context
     * @param \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository       $categoryRepository
     * @param \ACP3\Modules\ACP3\Categories\Cache                          $categoriesCache
     * @param \ACP3\Modules\ACP3\Categories\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Core\Helpers\FormToken                                 $formTokenHelper
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Categories\Model\Repository\CategoryRepository $categoryRepository,
        Categories\Cache $categoriesCache,
        Categories\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->categoryRepository = $categoryRepository;
        $this->categoriesCache = $categoriesCache;
        $this->adminFormValidation = $adminFormValidation;
        $this->formTokenHelper = $formTokenHelper;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $category = $this->categoryRepository->getOneById($id);

        if (empty($category) === false) {
            $this->title->setPageTitlePostfix($category['title']);

            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $category, $id);
            }

            return [
                'form' => array_merge($category, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param array $category
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $category, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $category, $id) {
            $file = $this->request->getFiles()->get('picture');

            $this->adminFormValidation
                ->setFile($file)
                ->setSettings($this->config->getSettings(Categories\Installer\Schema::MODULE_NAME))
                ->setCategoryId($id)
                ->validate($formData);

            $updateValues = [
                'title' => $this->get('core.helpers.secure')->strEncode($formData['title']),
                'description' => $this->get('core.helpers.secure')->strEncode($formData['description']),
            ];

            if (empty($file) === false) {
                $upload = new Core\Helpers\Upload($this->appPath, 'categories');
                $upload->removeUploadedFile($category['picture']);
                $result = $upload->moveFile($file->getPathname(), $file->getClientOriginalName());
                $updateValues['picture'] = $result['name'];
            }

            $bool = $this->categoryRepository->update($updateValues, $id);

            $this->categoriesCache->saveCache($this->categoryRepository->getModuleNameFromCategoryId($id));

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $bool;
        });
    }
}
