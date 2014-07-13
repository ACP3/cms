<?php

namespace ACP3\Modules\Contact\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Contact;

/**
 * Class Index
 * @package ACP3\Modules\Contact\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\Config
     */
    protected $contactConfig;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Lang $lang,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo,
        Core\Modules $modules,
        Core\Validate $validate,
        Core\Session $session,
        Core\Config $contactConfig)
    {
        parent::__construct($auth, $breadcrumb, $lang, $uri, $view, $seo, $modules, $validate, $session);

        $this->contactConfig = $contactConfig;
    }

    public function actionIndex()
    {
        $config = $this->contactConfig;

        if (empty($_POST) === false) {
            try {
                $validator = $this->get('contact.validator');
                $validator->validateSettings($_POST);

                $data = array(
                    'address' => Core\Functions::strEncode($_POST['address'], true),
                    'mail' => $_POST['mail'],
                    'telephone' => Core\Functions::strEncode($_POST['telephone']),
                    'fax' => Core\Functions::strEncode($_POST['fax']),
                    'disclaimer' => Core\Functions::strEncode($_POST['disclaimer'], true),
                );

                $bool = $config->setSettings($data);

                $this->session->unsetFormToken();

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/contact');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/contact');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        $this->redirectMessages()->getMessage();

        $settings = $config->getSettings();

        $this->view->assign('form', array_merge($settings, $_POST));

        $this->session->generateFormToken();
    }

}