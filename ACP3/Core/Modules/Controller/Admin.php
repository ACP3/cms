<?php

namespace ACP3\Core\Modules\Controller;

use ACP3\Core;

/**
 * Class Admin
 * @package ACP3\Core\Modules\Controller
 */
class Admin extends Core\Modules\Controller
{
    /**
     * @var \ACP3\Core\Session
     */
    protected $session;
    /**
     * @var Core\Validate
     */
    protected $validate;

    public function __construct(
        Core\Context $context,
        Core\Breadcrumb $breadcrumb,
        Core\SEO $seo,
        Core\Validate $validate,
        Core\Session $session)
    {
        parent::__construct($context, $breadcrumb, $seo);

        $this->validate = $validate;
        $this->session = $session;
    }

    /**
     * @return $this
     * @throws \ACP3\Core\Exceptions\UnauthorizedAccess
     */
    public function preDispatch()
    {
        if ($this->auth->isUser() === false) {
            $redirectUri = base64_encode('acp/' . $this->uri->query);
            $this->uri->redirect('users/index/login/redirect_' . $redirectUri);
        }

        return parent::preDispatch();
    }

    /**
     * Little helper function for deleting an result set
     *
     * @param string $moduleConfirmUrl
     * @param string $moduleIndexUrl
     * @return array
     */
    protected function _deleteItem($moduleConfirmUrl = '', $moduleIndexUrl = '')
    {
        if (isset($_POST['entries']) && is_array($_POST['entries']) === true) {
            $entries = $_POST['entries'];
        } elseif ($this->validate->deleteEntries($this->uri->entries) === true) {
            $entries = $this->uri->entries;
        }

        $alerts = new Core\Helpers\Alerts($this->uri, $this->view);

        if (!isset($entries)) {
            $this->setContent($alerts->errorBox($this->lang->t('system', 'no_entries_selected')));
        } elseif (is_array($entries) === true && $this->uri->action !== 'confirmed') {
            $data = array(
                'action' => 'confirmed',
                'entries' => $entries
            );
            $confirmBox = $alerts->confirmBoxPost($this->lang->t('system', 'confirm_delete'), $data, $this->uri->route($moduleConfirmUrl), $this->uri->route($moduleIndexUrl));
            $this->setContent($confirmBox);
        } else {
            return is_array($entries) ? $entries : explode('|', $entries);
        }
    }

    public function display()
    {
        // Content-Template automatisch setzen
        if ($this->getContentTemplate() === '') {
            $this->setContentTemplate($this->uri->mod . '/Admin/' . $this->uri->controller . '.' . $this->uri->file . '.tpl');
        }

        parent::display();
    }

}
