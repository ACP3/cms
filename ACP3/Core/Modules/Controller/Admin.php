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
     * @var \ACP3\Core\SessionHandler
     */
    protected $session;
    /**
     * @var \ACP3\Core\Validator\Rules\Misc
     */
    protected $validate;

    /**
     * @param \ACP3\Core\Context\Admin $adminContext
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

        if (empty($entries)) {
            $this->setTemplate($alerts->errorBoxContent($this->lang->t('system', 'no_entries_selected')));
        } elseif (empty($entries) === false && $this->request->action !== 'confirmed') {
            if (is_array($entries) === false) {
                $entries = [$entries];
            }

            $data = [
                'action' => 'confirmed',
                'entries' => $entries
            ];

            list($moduleConfirmUrl, $moduleIndexUrl) = $this->generateDefaultConfirmationBoxUris($moduleConfirmUrl, $moduleIndexUrl);

            $confirmBox = $alerts->confirmBoxPost(
                $this->fetchConfirmationBoxText($entries),
                $data,
                $this->router->route($moduleConfirmUrl),
                $this->router->route($moduleIndexUrl)
            );

            $this->setTemplate($confirmBox);
        } else {
            return is_array($entries) ? $entries : explode('|', $entries);
        }
    }

    /**
     * @param string|null $moduleConfirmUrl
     * @param string|null $moduleIndexUrl
     *
     * @return array
     */
    protected function generateDefaultConfirmationBoxUris($moduleConfirmUrl, $moduleIndexUrl)
    {
        if ($moduleConfirmUrl === null) {
            $moduleConfirmUrl = 'acp/' . $this->request->getModule() . '/' . $this->request->getController() . '/' . $this->request->getControllerAction();
        }

        if ($moduleIndexUrl === null) {
            $moduleIndexUrl = 'acp/' . $this->request->getModule() . '/' . $this->request->getController();
        }

        return [$moduleConfirmUrl, $moduleIndexUrl];
    }

    /**
     * @param array $entries
     *
     * @return mixed|string
     */
    protected function fetchConfirmationBoxText($entries)
    {
        $entriesCount = count($entries);

        if ($entriesCount === 1) {
            return $this->lang->t('system', 'confirm_delete_single');
        }

        return str_replace('{items}', $entriesCount, $this->lang->t('system', 'confirm_delete_multiple'));
    }
}
