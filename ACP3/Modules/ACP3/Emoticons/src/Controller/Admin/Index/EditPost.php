<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Emoticons;

class EditPost extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Validation\AdminFormValidation
     */
    private $adminFormValidation;
    /**
     * @var Emoticons\Model\EmoticonsModel
     */
    private $emoticonsModel;
    /**
     * @var \ACP3\Core\Helpers\Upload
     */
    private $emoticonsUploadHelper;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        WidgetContext $context,
        Action $actionHelper,
        Emoticons\Model\EmoticonsModel $emoticonsModel,
        Emoticons\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\Upload $emoticonsUploadHelper
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->emoticonsModel = $emoticonsModel;
        $this->emoticonsUploadHelper = $emoticonsUploadHelper;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $id)
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
