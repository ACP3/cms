<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Categories;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

class CreatePost extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\Context $context,
        private FormAction $actionHelper,
        private Categories\Model\CategoriesModel $categoriesModel,
        private Categories\Validation\AdminFormValidation $adminFormValidation,
        private Core\Helpers\Upload $categoriesUploadHelper
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|string|Response
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function __invoke(): array|string|Response
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
