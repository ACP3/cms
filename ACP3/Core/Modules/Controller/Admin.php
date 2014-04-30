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

    /**
     * @param Core\Auth $auth
     * @param Core\Breadcrumb $breadcrumb
     * @param Core\Date $date
     * @param \Doctrine\DBAL\Connection $db
     * @param Core\Lang $lang
     * @param Core\Session $session
     * @param Core\URI $uri
     * @param Core\View $view
     * @param Core\SEO $seo
     */
    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Core\Lang $lang,
        Core\Session $session,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view, $seo);
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
        } elseif (Core\Validate::deleteEntries($this->uri->entries) === true) {
            $entries = $this->uri->entries;
        }

        if (!isset($entries)) {
            $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'no_entries_selected')));
        } elseif (is_array($entries) === true && $this->uri->action !== 'confirmed') {
            $data = array(
                'action' => 'confirmed',
                'entries' => $entries
            );
            $confirmBox = Core\Functions::confirmBoxPost($this->lang->t('system', 'confirm_delete'), $data, $this->uri->route($moduleConfirmUrl), $this->uri->route($moduleIndexUrl));
            $this->view->setContent($confirmBox);
        } else {
            return is_array($entries) ? $entries : explode('|', $entries);
        }
    }

}
