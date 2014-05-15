<?php

namespace ACP3\Modules\Contact\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Contact;

/**
 * Description of ContactAdmin
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var Contact\Model
     */
    protected $model;

    protected function _init()
    {
        $this->model = new Contact\Model($this->db, $this->lang, $this->auth);
    }

    public function actionList()
    {
        if (empty($_POST) === false) {
            try {
                $this->model->validateSettings($_POST);

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
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/contact');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        Core\Functions::getRedirectMessage();

        $settings = Core\Config::getSettings('contact');

        $this->view->assign('form', array_merge($settings, $_POST));

        $this->session->generateFormToken();
    }

}