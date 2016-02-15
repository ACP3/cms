<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index
 */
class Edit extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Model\EmoticonRepository
     */
    protected $emoticonRepository;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Cache
     */
    protected $emoticonsCache;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext                  $context
     * @param \ACP3\Core\Helpers\FormToken                                $formTokenHelper
     * @param \ACP3\Modules\ACP3\Emoticons\Model\EmoticonRepository       $emoticonRepository
     * @param \ACP3\Modules\ACP3\Emoticons\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Emoticons\Cache                          $emoticonsCache
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Emoticons\Model\EmoticonRepository $emoticonRepository,
        Emoticons\Validation\AdminFormValidation $adminFormValidation,
        Emoticons\Cache $emoticonsCache)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->emoticonRepository = $emoticonRepository;
        $this->adminFormValidation = $adminFormValidation;
        $this->emoticonsCache = $emoticonsCache;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        $emoticon = $this->emoticonRepository->getOneById($id);

        if (empty($emoticon) === false) {
            if ($this->request->getPost()->isEmpty() === false) {
                return $this->executePost($this->request->getPost()->all(), $emoticon, $id);
            }

            $this->formTokenHelper->generateFormToken();

            return [
                'form' => array_merge($emoticon, $this->request->getPost()->all())
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array $formData
     * @param array $emoticon
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $emoticon, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $emoticon, $id) {
            $file = $this->request->getFiles()->get('picture');

            $this->adminFormValidation
                ->setFile($file)
                ->setSettings($this->config->getSettings('emoticons'))
                ->validate($formData);

            $updateValues = [
                'code' => Core\Functions::strEncode($formData['code']),
                'description' => Core\Functions::strEncode($formData['description']),
            ];

            if (empty($file) === false) {
                $upload = new Core\Helpers\Upload($this->appPath, 'emoticons');
                $upload->removeUploadedFile($emoticon['img']);
                $result = $upload->moveFile($file['tmp_name'], $file['name']);
                $updateValues['img'] = $result['name'];
            }

            $bool = $this->emoticonRepository->update($updateValues, $id);

            $this->emoticonsCache->saveCache();

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}
