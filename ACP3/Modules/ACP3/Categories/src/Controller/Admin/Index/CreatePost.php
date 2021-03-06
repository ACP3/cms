<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Categories;

class CreatePost extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
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
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Categories\Model\CategoriesModel $categoriesModel,
        Categories\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\Upload $categoriesUploadHelper
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->categoriesModel = $categoriesModel;
        $this->categoriesUploadHelper = $categoriesUploadHelper;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke()
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
}
