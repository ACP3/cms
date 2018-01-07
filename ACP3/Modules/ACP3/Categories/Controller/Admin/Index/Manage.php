<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

class Manage extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var Categories\Model\CategoriesModel
     */
    protected $categoriesModel;
    /**
     * @var Core\View\Block\RepositoryAwareFormBlockInterface
     */
    private $block;

    /**
     * Manage constructor.
     * @param Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\RepositoryAwareFormBlockInterface $block
     * @param Categories\Model\CategoriesModel $categoriesModel
     * @param Categories\Validation\AdminFormValidation $adminFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\RepositoryAwareFormBlockInterface $block,
        Categories\Model\CategoriesModel $categoriesModel,
        Categories\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->categoriesModel = $categoriesModel;
        $this->block = $block;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function execute(?int $id)
    {
        return $this->block
            ->setDataById($id)
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost(?int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();
            $file = $this->request->getFiles()->get('picture');

            $this->adminFormValidation
                ->setFile($file)
                ->setCategoryId($id)
                ->validate($formData);

            if (empty($file) === false) {
                $upload = new Core\Helpers\Upload($this->appPath, Categories\Installer\Schema::MODULE_NAME);

                if ($id !== null) {
                    $category = $this->categoriesModel->getOneById($id);
                    $upload->removeUploadedFile($category['picture']);
                }

                $result = $upload->moveFile($file->getPathname(), $file->getClientOriginalName());
                $formData['picture'] = $result['name'];
            }

            return $this->categoriesModel->save($formData, $id);
        });
    }
}
