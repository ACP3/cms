<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index
 */
class Edit extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Model\Repository\EmoticonRepository
     */
    protected $emoticonRepository;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var Emoticons\Model\EmoticonsModel
     */
    protected $emoticonsModel;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param Emoticons\Model\EmoticonsModel $emoticonsModel
     * @param \ACP3\Modules\ACP3\Emoticons\Model\Repository\EmoticonRepository $emoticonRepository
     * @param \ACP3\Modules\ACP3\Emoticons\Validation\AdminFormValidation $adminFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Emoticons\Model\EmoticonsModel $emoticonsModel,
        Emoticons\Model\Repository\EmoticonRepository $emoticonRepository,
        Emoticons\Validation\AdminFormValidation $adminFormValidation)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->emoticonRepository = $emoticonRepository;
        $this->adminFormValidation = $adminFormValidation;
        $this->emoticonsModel = $emoticonsModel;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $emoticon = $this->emoticonRepository->getOneById($id);

        if (empty($emoticon) === false) {
            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $emoticon, $id);
            }

            return [
                'form' => array_merge($emoticon, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param array $emoticon
     * @param int   $emoticonId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $emoticon, $emoticonId)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $emoticon, $emoticonId) {
            $file = $this->request->getFiles()->get('picture');

            $this->adminFormValidation
                ->setFile($file)
                ->setSettings($this->config->getSettings(Emoticons\Installer\Schema::MODULE_NAME))
                ->validate($formData);

            if (empty($file) === false) {
                $upload = new Core\Helpers\Upload($this->appPath, Emoticons\Installer\Schema::MODULE_NAME);
                $upload->removeUploadedFile($emoticon['img']);
                $result = $upload->moveFile($file->getPathname(), $file->getClientOriginalName());
                $formData['img'] = $result['name'];
            }

            return $this->emoticonsModel->saveEmoticon($formData, $emoticonId);
        });
    }
}
