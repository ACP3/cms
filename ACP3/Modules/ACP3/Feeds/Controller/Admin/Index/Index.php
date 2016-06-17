<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Feeds;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Feeds\Controller\Admin\Index
 */
class Index extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Feeds\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * @param \ACP3\Core\Controller\Context\AdminContext              $context
     * @param \ACP3\Core\Helpers\Forms                                $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                            $formTokenHelper
     * @param \ACP3\Modules\ACP3\Feeds\Validation\AdminFormValidation $adminFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Feeds\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->adminFormValidation = $adminFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->count() !== 0) {
            return $this->indexPost($this->request->getPost()->all());
        }

        $settings = $this->config->getSettings('feeds');

        $feedTypes = [
            'RSS 1.0' => 'RSS 1.0',
            'RSS 2.0' => 'RSS 2.0',
            'ATOM' => 'ATOM'
        ];

        return [
            'feed_types' => $this->formsHelper->choicesGenerator('feed_type', $feedTypes, $settings['feed_type']),
            'form' => array_merge($settings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function indexPost(array $formData)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData) {
            $this->adminFormValidation->validate($formData);

            $data = [
                'feed_image' => $this->get('core.helpers.secure')->strEncode($formData['feed_image']),
                'feed_type' => $formData['feed_type']
            ];

            return $this->config->setSettings($data, 'feeds');
        });
    }
}
