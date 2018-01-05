<?php
namespace ACP3\Installer\Core\Helpers;

class Alerts extends \ACP3\Core\Helpers\Alerts
{
    /**
     * @inheritdoc
     */
    public function errorBox($errors)
    {
        $this->setErrorBoxData($errors);

        return $this->view->fetchTemplate('error_box.tpl');
    }
}
