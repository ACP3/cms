<?php

namespace ACP3\Core\Modules\Controller;

use ACP3\Core;

/**
 * Class Admin
 * @package ACP3\Core\Modules\Controller
 */
abstract class Admin extends Core\Modules\Controller\Frontend
{
    /**
     * @var \ACP3\Core\Session
     */
    protected $session;
    /**
     * @var \ACP3\Core\Validator\Rules\Misc
     */
    protected $validate;
    /**
     * @var Core\Router\Aliases
     */
    protected $aliases;

    public function __construct(Core\Context\Admin $adminContext)
    {
        parent::__construct($adminContext);

        $this->validate = $adminContext->getValidate();
        $this->session = $adminContext->getSession();
        $this->aliases = $adminContext->getAliases();
    }

    /**
     * @return $this
     * @throws \ACP3\Core\Exceptions\UnauthorizedAccess
     */
    public function preDispatch()
    {
        if ($this->auth->isUser() === false) {
            $redirectUri = base64_encode('acp/' . $this->request->query);
            $this->redirect()->temporary('users/index/login/redirect_' . $redirectUri);
        }

        return parent::preDispatch();
    }

    public function display()
    {
        // Content-Template automatisch setzen
        if ($this->getContentTemplate() === '') {
            $this->setContentTemplate($this->request->mod . '/Admin/' . $this->request->controller . '.' . $this->request->file . '.tpl');
        }

        parent::display();
    }

    /**
     * Little helper function for deleting an result set
     *
     * @param string $moduleConfirmUrl
     * @param string $moduleIndexUrl
     *
     * @return array
     */
    protected function _deleteItem($moduleConfirmUrl = '', $moduleIndexUrl = '')
    {
        if (isset($_POST['entries']) && is_array($_POST['entries']) === true) {
            $entries = $_POST['entries'];
        } elseif ((bool)preg_match('/^((\d+)\|)*(\d+)$/', $this->request->entries) === true) {
            $entries = $this->request->entries;
        }

        $alerts = $this->get('core.helpers.alerts');

        if (!isset($entries)) {
            $this->setContent($alerts->errorBox($this->lang->t('system', 'no_entries_selected')));
        } elseif (is_array($entries) === true && $this->request->action !== 'confirmed') {
            $data = array(
                'action' => 'confirmed',
                'entries' => $entries
            );
            $confirmBox = $alerts->confirmBoxPost($this->lang->t('system', 'confirm_delete'), $data, $this->router->route($moduleConfirmUrl), $this->router->route($moduleIndexUrl));
            $this->setContent($confirmBox);
        } else {
            return is_array($entries) ? $entries : explode('|', $entries);
        }
    }

}
