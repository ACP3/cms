<?php

namespace ACP3\Core\Modules;

use ACP3\Core;
use ACP3\Core\Modules\Controller\Context;

/**
 * Class AdminController
 * @package ACP3\Core\Modules
 */
abstract class AdminController extends Core\Modules\FrontendController
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
     * @param \ACP3\Core\Modules\Controller\AdminContext $adminContext
     */
    public function __construct(Controller\AdminContext $adminContext)
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
        if ($this->request->getPost()->has('entries') && is_array($this->request->getPost()->get('entries')) === true) {
            $entries = $this->request->getPost()->get('entries');
        } elseif ((bool)preg_match('/^((\d+)\|)*(\d+)$/', $this->request->getParameters()->get('entries')) === true) {
            $entries = $this->request->getParameters()->get('entries');
        }

        /** @var \ACP3\Core\Helpers\Alerts $alerts */
        $alerts = $this->get('core.helpers.alerts');

        if (empty($entries)) {
            $this->setTemplate($alerts->errorBoxContent($this->lang->t('system', 'no_entries_selected')));
        } elseif (empty($entries) === false && $this->request->getParameters()->get('action') !== 'confirmed') {
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
            $moduleConfirmUrl = $this->request->getFullPath();
        }

        if ($moduleIndexUrl === null) {
            $moduleIndexUrl = $this->request->getModuleAndController();
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
