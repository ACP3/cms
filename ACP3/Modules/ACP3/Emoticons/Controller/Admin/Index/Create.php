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
class Create extends Core\Controller\AdminAction
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
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext                  $context
     * @param \ACP3\Core\Helpers\FormToken                                $formTokenHelper
     * @param \ACP3\Modules\ACP3\Emoticons\Model\EmoticonRepository       $emoticonRepository
     * @param \ACP3\Modules\ACP3\Emoticons\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Emoticons\Cache                          $emoticonsCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
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
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false) {
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
                ->setSettings($this->config->getSettings('emoticons'))
                ->setFileRequired(true)
                ->validate($formData);

            $upload = new Core\Helpers\Upload($this->appPath, 'emoticons');
            $result = $upload->moveFile($file['tmp_name'], $file['name']);

            $insertValues = [
                'id' => '',
                'code' => $this->get('core.helpers.secure')->strEncode($formData['code']),
                'description' => $this->get('core.helpers.secure')->strEncode($formData['description']),
                'img' => $result['name'],
            ];

            $bool = $this->emoticonRepository->insert($insertValues);

            $this->emoticonsCache->saveCache();

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}
