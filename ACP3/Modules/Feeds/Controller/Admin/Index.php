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
    /**
     *
     * @var Feeds\Model
     */
    protected $model;

    protected function _init()
    {
        $this->model = new Feeds\Model($this->db, $this->lang);
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            try {
                $this->model->validateSettings($_POST);

                $data = array(
                    'feed_image' => Core\Functions::strEncode($_POST['feed_image']),
                    'feed_type' => $_POST['feed_type']
                );

                $bool = Core\Config::setSettings('feeds', $data);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/feeds');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/feeds');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        Core\Functions::getRedirectMessage();

        $settings = Core\Config::getSettings('feeds');

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