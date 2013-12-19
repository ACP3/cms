<?php

namespace ACP3\Modules\Feeds\Controller;

use ACP3\Core;

/**
 * Description of FeedsAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller\Admin
{
    public function __construct(
        \ACP3\Core\Auth $auth,
        \ACP3\Core\Breadcrumb $breadcrumb,
        \ACP3\Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        \ACP3\Core\Lang $lang,
        \ACP3\Core\Session $session,
        \ACP3\Core\URI $uri,
        \ACP3\Core\View $view)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view);
    }

    public function actionList()
    {
        if (isset($_POST['submit']) === true) {
            if (empty($_POST['feed_type']) || in_array($_POST['feed_type'], array('RSS 1.0', 'RSS 2.0', 'ATOM')) === false)
                $errors['mail'] = $this->lang->t('feeds', 'select_feed_type');

            if (isset($errors) === true) {
                $this->view->assign('error_msg', Core\Functions::errorBox($errors));
            } elseif (Core\Validate::formToken() === false) {
                $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
            } else {
                $data = array(
                    'feed_image' => Core\Functions::strEncode($_POST['feed_image']),
                    'feed_type' => $_POST['feed_type']
                );

                $bool = Core\Config::setSettings('feeds', $data);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/feeds');
            }
        }
        if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
            Core\Functions::getRedirectMessage();

            $settings = Core\Config::getSettings('feeds');

            $feed_type = array(
                'RSS 1.0',
                'RSS 2.0',
                'ATOM'
            );
            $this->view->assign('feed_types', Core\Functions::selectGenerator('feed_type', $feed_type, $feed_type, $settings['feed_type']));

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

            $this->session->generateFormToken();
        }
    }

}