<?php

namespace ACP3\Modules\ACP3\Feeds\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Feeds;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Feeds\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Feeds\Validator
     */
    protected $feedsValidator;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\Helpers\FormToken               $formTokenHelper
     * @param \ACP3\Modules\ACP3\Feeds\Validator         $feedsValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Feeds\Validator $feedsValidator)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->feedsValidator = $feedsValidator;
    }

    public function actionIndex()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_indexPost($this->request->getPost()->getAll());
        }

        $settings = $this->config->getSettings('feeds');

        $feedType = [
            'RSS 1.0',
            'RSS 2.0',
            'ATOM'
        ];
        $this->view->assign('feed_types', $this->get('core.helpers.forms')->selectGenerator('feed_type', $feedType, $feedType, $settings['feed_type']));

        $this->view->assign('form', array_merge($settings, $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    /**
     * @param array $formData
     */
    protected function _indexPost(array $formData)
    {
        try {
            $this->feedsValidator->validateSettings($formData);

            $data = [
                'feed_image' => Core\Functions::strEncode($formData['feed_image']),
                'feed_type' => $formData['feed_type']
            ];

            $bool = $this->config->setSettings($data, 'feeds');

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
