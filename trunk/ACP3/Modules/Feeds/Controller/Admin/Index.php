<?php

namespace ACP3\Modules\Feeds\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Feeds;

/**
 * Description of FeedsAdmin
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Admin
{
    public function actionIndex()
    {
        $config = new Core\Config($this->db, 'feeds');

        $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);

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