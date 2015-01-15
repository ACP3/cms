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
     * @param Core\Context\Admin $adminContext
     */
    public function __construct(Core\Context\Admin $adminContext)
    {
        parent::__construct($adminContext);

        $this->validate = $adminContext->getValidate();
        $this->session = $adminContext->getSession();
    }

    /**
     * @return $this
     * @throws \ACP3\Core\Exceptions\UnauthorizedAccess
     */
    public function preDispatch()
    {
        if ($this->auth->isUser() === false) {
            throw new Core\Exceptions\UnauthorizedAccess();
        }

        return parent::preDispatch();
    }

    /**
     * Little helper function for deleting an result set
     *
     * @param string|null $moduleConfirmUrl
     * @param string|null $moduleIndexUrl
     *
     * @return array
     */
    protected function _deleteItem($moduleConfirmUrl = null, $moduleIndexUrl = null)
    {
        if (isset($_POST['entries']) && is_array($_POST['entries']) === true) {
            $entries = $_POST['entries'];
        } elseif ((bool)preg_match('/^((\d+)\|)*(\d+)$/', $this->request->entries) === true) {
            $entries = $this->request->entries;
        }

        /** @var \ACP3\Core\Helpers\Alerts $alerts */
        $alerts = $this->get('core.helpers.alerts');

        if (!isset($entries)) {
            $this->setTemplate($alerts->errorBoxContent($this->lang->t('system', 'no_entries_selected')));
        } elseif (empty($entries) === false && $this->request->action !== 'confirmed') {
            if (is_array($entries) === false) {
                $entries = [$entries];
            }

            $data = [
                'action' => 'confirmed',
                'entries' => $entries
            ];

            if ($moduleConfirmUrl === null) {
                $moduleConfirmUrl = 'acp/' . $this->request->mod . '/' . $this->request->controller . '/' . $this->request->file;
            }

            if ($moduleIndexUrl === null) {
                $moduleIndexUrl = 'acp/' . $this->request->mod . '/' . $this->request->controller;
            }

            $confirmationText = count($entries) == 1 ? $this->lang->t('system', 'confirm_delete_single') : str_replace('{items}', count($entries), $this->lang->t('system', 'confirm_delete_multiple'));

            $confirmBox = $alerts->confirmBoxPost($confirmationText, $data, $this->router->route($moduleConfirmUrl), $this->router->route($moduleIndexUrl));

            $this->setTemplate($confirmBox);
        } else {
            return is_array($entries) ? $entries : explode('|', $entries);
        }
    }
}
