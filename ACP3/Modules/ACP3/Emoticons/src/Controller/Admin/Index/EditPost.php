<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\Context;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Emoticons;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

class EditPost extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Context $context,
        private readonly FormAction $actionHelper,
        private readonly Emoticons\Model\EmoticonsModel $emoticonsModel,
        private readonly Emoticons\Validation\AdminFormValidation $adminFormValidation,
        private readonly Core\Helpers\Upload $emoticonsUploadHelper
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|string|Response
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function __invoke(int $id): array|string|Response
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();
            $emoticon = $this->emoticonsModel->getOneById($id);
            $file = $this->request->getFiles()->get('picture');

            $this->adminFormValidation
                ->setFile($file)
                ->setSettings($this->config->getSettings(Emoticons\Installer\Schema::MODULE_NAME))
                ->validate($formData);

            if (empty($file) === false) {
                $this->emoticonsUploadHelper->removeUploadedFile($emoticon['img']);
                $result = $this->emoticonsUploadHelper->moveFile($file->getPathname(), $file->getClientOriginalName());
                $formData['img'] = $result['name'];
            }

            return $this->emoticonsModel->save($formData, $id);
        });
    }
}
