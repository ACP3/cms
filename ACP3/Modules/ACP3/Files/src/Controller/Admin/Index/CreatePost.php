<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CreatePost extends AbstractFormAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private FormAction $actionHelper,
        private UserModelInterface $user,
        private Files\Model\FilesModel $filesModel,
        private Files\Validation\AdminFormValidation $adminFormValidation,
        private Core\Helpers\Upload $filesUploadHelper,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context, $categoriesHelpers);
    }

    /**
     * @return array<string, mixed>|string|JsonResponse|RedirectResponse
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function __invoke(): array|string|JsonResponse|RedirectResponse
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();
            if (isset($formData['external'])) {
                $file = $formData['file_external'];
            } else {
                $file = $this->request->getFiles()->get('file_internal');
            }

            $this->adminFormValidation
                ->setFile($file)
                ->validate($formData);

            if ($file instanceof UploadedFile) {
                $result = $this->filesUploadHelper->moveFile($file->getPathname(), $file->getClientOriginalName());
                $formData['file'] = $result['name'];
                $formData['filesize'] = $result['size'];
            } else {
                $formData['file'] = $file;
                $formData['filesize'] = ((float) $formData['filesize']) . ' ' . $formData['unit'];
            }

            $formData['cat'] = $this->fetchCategoryId($formData);
            $formData['user_id'] = $this->user->getUserId();

            return $this->filesModel->save($formData);
        });
    }
}
