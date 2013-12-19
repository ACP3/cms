<?php

namespace ACP3\Core\Modules\Controller;

use ACP3\Core;

/**
 * Description of AdminController
 *
 * @author goratsch
 */
class Admin extends Core\Modules\Controller
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

    protected function _deleteItem($moduleConfirmUrl = '', $moduleIndexUrl = '')
    {
        if (isset($_POST['entries']) && is_array($_POST['entries']) === true) {
            $entries = $_POST['entries'];
        } elseif (Core\Validate::deleteEntries($this->uri->entries) === true) {
            $entries = $this->uri->entries;
        }

        if (!isset($entries)) {
            $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'no_entries_selected')));
        } elseif (is_array($entries) === true) {
            $marked_entries = implode('|', $entries);
            $this->view->setContent(Core\Functions::confirmBox($this->lang->t('system', 'confirm_delete'), $this->uri->route($moduleConfirmUrl) . 'entries_' . $marked_entries . '/action_confirmed/', $this->uri->route($moduleIndexUrl)));
        } else {
            return $entries;
        }
    }

}
