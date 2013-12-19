<?php

namespace ACP3\Modules\Contact\Controller;

use ACP3\Core;
use ACP3\Modules\Contact;

/**
 * Description of ContactAdmin
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
            if (!empty($_POST['mail']) && Core\Validate::email($_POST['mail']) === false)
                $errors['mail'] = $this->lang->t('system', 'wrong_email_format');

            if (isset($errors) === true) {
                $this->view->assign('error_msg', Core\Functions::errorBox($errors));
            } elseif (Core\Validate::formToken() === false) {
                $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
            } else {
                $data = array(
                    'address' => Core\Functions::strEncode($_POST['address'], true),
                    'mail' => $_POST['mail'],
                    'telephone' => Core\Functions::strEncode($_POST['telephone']),
                    'fax' => Core\Functions::strEncode($_POST['fax']),
                    'disclaimer' => Core\Functions::strEncode($_POST['disclaimer'], true),
                );

                $bool = Core\Config::setSettings('contact', $data);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/contact');
            }
        }
        if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
            Core\Functions::getRedirectMessage();

            $settings = Core\Config::getSettings('contact');

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

            $this->session->generateFormToken();
        }
    }

}