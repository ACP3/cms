<?php

namespace ACP3\Modules\ACP3\Feeds\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Feeds;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Feeds\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Feeds\Validator
     */
    protected $feedsValidator;

    /**
     * @param \ACP3\Core\Context\Admin      $context
     * @param \ACP3\Core\Helpers\Secure     $secureHelper
     * @param \ACP3\Modules\ACP3\Feeds\Validator $feedsValidator
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Helpers\Secure $secureHelper,
        Feeds\Validator $feedsValidator)
    {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->feedsValidator = $feedsValidator;
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            $this->_indexPost($_POST);
        }

        $settings = $this->config->getSettings('feeds');

        $feedType = [
            'RSS 1.0',
            'RSS 2.0',
            'ATOM'
        ];
        $this->view->assign('feed_types', $this->get('core.helpers.forms')->selectGenerator('feed_type', $feedType, $feedType, $settings['feed_type']));

        $this->view->assign('form', array_merge($settings, $_POST));

        $this->secureHelper->generateFormToken($this->request->getQuery());
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

            $this->secureHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
