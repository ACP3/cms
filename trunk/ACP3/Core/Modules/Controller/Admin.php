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
            $this->setContent(Core\Functions::errorBox($this->lang->t('system', 'no_entries_selected')));
        } elseif (is_array($entries) === true && $this->uri->action !== 'confirmed') {
            $data = array(
                'action' => 'confirmed',
                'entries' => $entries
            );
            $confirmBox = Core\Functions::confirmBoxPost($this->lang->t('system', 'confirm_delete'), $data, $this->uri->route($moduleConfirmUrl), $this->uri->route($moduleIndexUrl));
            $this->setContent($confirmBox);
        } else {
            return is_array($entries) ? $entries : explode('|', $entries);
        }
    }

    public function display()
    {
        // Content-Template automatisch setzen
        if ($this->getContentTemplate() === '') {
            $this->setContentTemplate($this->uri->mod . '/Admin/' . $this->uri->file . '.tpl');
        }

        parent::display();
    }

}
