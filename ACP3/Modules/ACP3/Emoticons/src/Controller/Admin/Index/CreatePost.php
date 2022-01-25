<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Emoticons;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class CreatePost extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        WidgetContext $context,
        private FormAction $actionHelper,
        private Emoticons\Model\EmoticonsModel $emoticonsModel,
        private Emoticons\Validation\AdminFormValidation $adminFormValidation,
        private Core\Helpers\Upload $emoticonsUploadHelper
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|string|JsonResponse|RedirectResponse|Response
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function __invoke(): array|string|JsonResponse|RedirectResponse|Response
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();
            $file = $this->request->getFiles()->get('picture');

            $this->adminFormValidation
                ->setFile($file)
                ->setSettings($this->config->getSettings(Emoticons\Installer\Schema::MODULE_NAME))
                ->setFileRequired(true)
                ->validate($formData);

            $result = $this->emoticonsUploadHelper->moveFile($file->getPathname(), $file->getClientOriginalName());
            $formData['img'] = $result['name'];

            return $this->emoticonsModel->save($formData);
        });
    }
}
