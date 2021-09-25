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
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreatePost extends AbstractFormAction
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
     * @var \ACP3\Core\Helpers\FormAction
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        FormAction $actionHelper,
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
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke()
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
