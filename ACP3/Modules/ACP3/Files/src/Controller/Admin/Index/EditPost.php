<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files;
use ACP3\Modules\ACP3\Files\Helpers;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EditPost extends AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Files\Validation\AdminFormValidation
     */
    private $adminFormValidation;
    /**
     * @var Files\Model\FilesModel
     */
    private $filesModel;
    /**
     * @var \ACP3\Core\Helpers\Upload
     */
    private $filesUploadHelper;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Action $actionHelper,
        UserModelInterface $user,
        Files\Model\FilesModel $filesModel,
        Files\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\Upload $filesUploadHelper,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context, $categoriesHelpers);

        $this->adminFormValidation = $adminFormValidation;
        $this->filesModel = $filesModel;
        $this->filesUploadHelper = $filesUploadHelper;
        $this->user = $user;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $id)
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
                ->setUriAlias(\sprintf(Helpers::URL_KEY_PATTERN, $id))
                ->validate($formData);

            $formData['cat'] = $this->fetchCategoryId($formData);
            $formData['user_id'] = $this->user->getUserId();

            if (!empty($file)) {
                $newFileSql = $this->updateAssociatedFile($file, $formData, $dl['file']);

                $formData = \array_merge($formData, $newFileSql);
            }

            return $this->filesModel->save($formData, $id);
        });
    }

    /**
     * @param string|UploadedFile $file
     *
     * @throws \ACP3\Core\Validation\Exceptions\ValidationFailedException
     */
    private function updateAssociatedFile($file, array $formData, string $currentFileName): array
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
