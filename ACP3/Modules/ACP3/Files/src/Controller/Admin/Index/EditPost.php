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
use ACP3\Modules\ACP3\Files\Helpers;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EditPost extends AbstractFormAction
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
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id): array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();
            $file = null;
            if (isset($formData['external'])) {
                $file = $formData['file_external'];
            } elseif ($this->request->getFiles()->has('file_internal')) {
                $file = $this->request->getFiles()->get('file_internal');
            }
            $dl = $this->filesModel->getOneById($id);

            $this->adminFormValidation
                ->setFile($file)
                ->setUriAlias(sprintf(Helpers::URL_KEY_PATTERN, $id))
                ->validate($formData);

            $formData['cat'] = $this->fetchCategoryId($formData);
            $formData['user_id'] = $this->user->getUserId();

            if (!empty($file)) {
                $newFileSql = $this->updateAssociatedFile($file, $formData, $dl['file']);

                $formData = array_merge($formData, $newFileSql);
            }

            return $this->filesModel->save($formData, $id);
        });
    }

    /**
     * @throws \ACP3\Core\Validation\Exceptions\ValidationFailedException
     */
    private function updateAssociatedFile(string|UploadedFile $file, array $formData, string $currentFileName): array
    {
        if ($file instanceof UploadedFile) {
            $result = $this->filesUploadHelper->moveFile($file->getPathname(), $file->getClientOriginalName());
            $newFile = $result['name'];
            $fileSize = $result['size'];
        } else {
            $formData['filesize'] = (float) $formData['filesize'];
            $newFile = $file;
            $fileSize = $formData['filesize'] . ' ' . $formData['unit'];
        }

        $this->filesUploadHelper->removeUploadedFile($currentFileName);

        return [
            'file' => $newFile,
            'filesize' => $fileSize,
        ];
    }
}
