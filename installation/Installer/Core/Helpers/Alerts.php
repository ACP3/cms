<?php
namespace ACP3\Installer\Core\Helpers;

use ACP3\Installer\Core;

/**
 * Class Alerts
 * @package ACP3\Installer\Core\Helpers
 */
class Alerts extends \ACP3\Core\Helpers\Alerts
{
    /**
     * @inheritdoc
     */
    public function errorBox($errors, $contentOnly = true)
    {
        $this->setErrorBoxData($errors);

        return $this->view->fetchTemplate('error_box.tpl');
    }
}
