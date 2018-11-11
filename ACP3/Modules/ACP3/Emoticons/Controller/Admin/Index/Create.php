<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;

class Create extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var Emoticons\Model\EmoticonsModel
     */
    protected $emoticonsModel;
    /**
     * @var \ACP3\Core\Helpers\Upload
     */
    private $emoticonsUploadHelper;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Emoticons\Model\EmoticonsModel $emoticonsModel,
        Emoticons\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\Upload $emoticonsUploadHelper
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->adminFormValidation = $adminFormValidation;
        $this->emoticonsModel = $emoticonsModel;
        $this->emoticonsUploadHelper = $emoticonsUploadHelper;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return [
            'form' => \array_merge(['code' => '', 'description' => ''], $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost()
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
