<?php
namespace ACP3\Core\Helpers;

use ACP3\Core;

/**
 * Class Alerts
 * @package ACP3\Core\Helpers
 */
class Alerts
{
    /**
     * @var Core\View
     */
    private $view;
    /**
     * @var Core\URI
     */
    private $uri;

    public function __construct(Core\URI $uri, Core\View $view)
    {
        $this->uri = $uri;
        $this->view = $view;
    }

    /**
     * Displays a confirm box
     *
     * @param string $text
     * @param int|string|array $forward
     * @param int|string $backward
     * @param integer $overlay
     * @return string
     */
    public function confirmBox($text, $forward = 0, $backward = 0, $overlay = 0)
    {
        if (!empty($text)) {
            $confirm = array(
                'text' => $text,
                'forward' => $forward,
                'overlay' => $overlay,
            );
            if (!empty($backward)) {
                $confirm['backward'] = $backward;
            }

            $this->view->assign('confirm', $confirm);

            return $this->view->fetchTemplate('system/confirm_box.tpl');
        }
        return '';
    }

    /**
     * Displays a confirm box, where the forward button triggers a form submit using POST
     *
     * @param $text
     * @param array $data
     * @param $forward
     * @param int $backward
     * @return string
     */
    public function confirmBoxPost($text, array $data, $forward, $backward = 0)
    {
        if (!empty($text) && !empty($data)) {
            $confirm = array(
                'text' => $text,
                'data' => $data,
                'forward' => $forward,
            );
            if (!empty($backward)) {
                $confirm['backward'] = $backward;
            }

            $this->view->assign('confirm', $confirm);

            return $this->view->fetchTemplate('system/confirm_box_post.tpl');
        }
        return '';
    }

    /**
     * Gibt eine Box mit den aufgetretenen Fehlern aus
     *
     * @param string|array $errors
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
                if (Core\Validate::isNumber($key) === false) {
                    $hasNonIntegerKeys = true;
                    break;
                }
            }
        } else {
            $errors = (array)$errors;
        }
        $this->view->assign('error_box', array('non_integer_keys' => $hasNonIntegerKeys, 'errors' => $errors));
        $content = $this->view->fetchTemplate('system/error_box.tpl');

        if ($this->uri->getIsAjax() === true) {
            $return = array(
                'success' => false,
                'content' => $content,
            );

            Core\Functions::outputJson($return);
        }
        return $content;
    }

} 