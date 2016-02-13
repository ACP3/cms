<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Feeds;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Feeds\Controller\Admin\Index
 */
class Index extends Core\Modules\AdminController
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
     * @param \ACP3\Core\Modules\Controller\AdminContext              $context
     * @param \ACP3\Core\Helpers\FormToken                            $formTokenHelper
     * @param \ACP3\Modules\ACP3\Feeds\Validation\AdminFormValidation $adminFormValidation
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Feeds\Validation\AdminFormValidation $adminFormValidation
    )
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->adminFormValidation = $adminFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->indexPost($this->request->getPost()->all());
        }

        $settings = $this->config->getSettings('feeds');

        $feedType = [
            'RSS 1.0',
            'RSS 2.0',
            'ATOM'
        ];

        $this->formTokenHelper->generateFormToken();

        return [
            'feed_types' => $this->get('core.helpers.forms')->selectGenerator('feed_type', $feedType, $feedType, $settings['feed_type']),
            'form' => array_merge($settings, $this->request->getPost()->all())
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
                'feed_image' => Core\Functions::strEncode($formData['feed_image']),
                'feed_type' => $formData['feed_type']
            ];

            $this->formTokenHelper->unsetFormToken();

            return $this->config->setSettings($data, 'feeds');
        });
    }
}
