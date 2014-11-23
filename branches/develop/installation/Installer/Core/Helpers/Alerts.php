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
     * Gibt eine Box mit den aufgetretenen Fehlern aus
     *
     * @param string|array $errors
     *
     * @return string
     */
    public function errorBox($errors)
    {
        $hasNonIntegerKeys = false;

        if (is_string($errors) && ($data = @unserialize($errors)) !== false) {
            $errors = $data;
        }

        if (is_array($errors) === true) {
            foreach (array_keys($errors) as $key) {
                if (is_numeric($key) === false) {
                    $hasNonIntegerKeys = true;
                    break;
                }
            }
        } else {
            $errors = (array)$errors;
        }
        $this->view->assign('error_box', array('non_integer_keys' => $hasNonIntegerKeys, 'errors' => $errors));
        $content = $this->view->fetchTemplate('error_box.tpl');

        if ($this->request->getIsAjax() === true) {
            $return = array(
                'success' => false,
                'content' => $content,
            );

            $this->outputHelper->outputJson($return);
        }
        return $content;
    }

}