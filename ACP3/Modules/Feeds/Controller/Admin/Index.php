<?php

namespace ACP3\Modules\Feeds\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Feeds;

/**
 * Class Index
 * @package ACP3\Modules\Feeds\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Core\Config
     */
    protected $feedsConfig;

    public function __construct(
        Core\Context\Admin $context,
        Core\Helpers\Secure $secureHelper,
        Core\Config $feedsConfig)
    {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->feedsConfig = $feedsConfig;
    }

    public function actionIndex()
    {
        $redirect = $this->redirectMessages();

        if (empty($_POST) === false) {
            try {
                $validator = $this->get('feeds.validator');
                $validator->validateSettings($_POST);

                $data = array(
                    'feed_image' => Core\Functions::strEncode($_POST['feed_image']),
                    'feed_type' => $_POST['feed_type']
                );

                $bool = $this->feedsConfig->setSettings($data);

                $this->secureHelper->unsetFormToken($this->request->query);

                $redirect->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/feeds');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $redirect->setMessage(false, $e->getMessage(), 'acp/feeds');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
            }
        }

        $redirect->getMessage();

        $settings = $this->feedsConfig->getSettings();

        $feedType = array(
            'RSS 1.0',
            'RSS 2.0',
            'ATOM'
        );
        $this->view->assign('feed_types', Core\Functions::selectGenerator('feed_type', $feedType, $feedType, $settings['feed_type']));

        $this->view->assign('form', array_merge($settings, $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

}