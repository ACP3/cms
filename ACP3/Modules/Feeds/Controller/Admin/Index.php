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
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    public function __construct(
        Core\Context $context,
        Core\Breadcrumb $breadcrumb,
        Core\SEO $seo,
        Core\Validate $validate,
        Core\Session $session,
        \Doctrine\DBAL\Connection $db)
    {
        parent::__construct($context, $breadcrumb, $seo, $validate, $session);

        $this->db = $db;
    }

    public function actionIndex()
    {
        $config = new Core\Config($this->db, 'feeds');

        $redirect = $this->redirectMessages();

        if (empty($_POST) === false) {
            try {
                $validator = $this->get('feeds.validator');
                $validator->validateSettings($_POST);

                $data = array(
                    'feed_image' => Core\Functions::strEncode($_POST['feed_image']),
                    'feed_type' => $_POST['feed_type']
                );

                $bool = $config->setSettings($data);

                $this->session->unsetFormToken();

                $redirect->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/feeds');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $redirect->setMessage(false, $e->getMessage(), 'acp/feeds');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        $redirect->getMessage();

        $settings = $config->getSettings();

        $feedType = array(
            'RSS 1.0',
            'RSS 2.0',
            'ATOM'
        );
        $this->view->assign('feed_types', Core\Functions::selectGenerator('feed_type', $feedType, $feedType, $settings['feed_type']));

        $this->view->assign('form', array_merge($settings, $_POST));

        $this->session->generateFormToken();
    }

}