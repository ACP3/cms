<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index
 */
class Create extends Core\Controller\AbstractAdminAction
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
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param Emoticons\Model\EmoticonsModel $emoticonsModel
     * @param \ACP3\Modules\ACP3\Emoticons\Validation\AdminFormValidation $adminFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Emoticons\Model\EmoticonsModel $emoticonsModel,
        Emoticons\Validation\AdminFormValidation $adminFormValidation)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->adminFormValidation = $adminFormValidation;
        $this->emoticonsModel = $emoticonsModel;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->count() !== 0) {
            return $this->executePost($this->request->getPost()->all());
        }

        return [
            'form' => array_merge(['code' => '', 'description' => ''], $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData) {
            $file = $this->request->getFiles()->get('picture');

            $this->adminFormValidation
                ->setFile($file)
                ->setSettings($this->config->getSettings(Emoticons\Installer\Schema::MODULE_NAME))
                ->setFileRequired(true)
                ->validate($formData);

            $upload = new Core\Helpers\Upload($this->appPath, Emoticons\Installer\Schema::MODULE_NAME);
            $result = $upload->moveFile($file->getPathname(), $file->getClientOriginalName());
            $formData['img'] = $result['name'];

            return $this->emoticonsModel->saveEmoticon($formData);
        });
    }
}
