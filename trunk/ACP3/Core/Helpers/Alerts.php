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
     * @var \ACP3\Core\Helpers\Output
     */
    protected $outputHelper;
    /**
     * @var Core\View
     */
    protected $view;
    /**
     * @var Core\Request
     */
    protected $request;

    /**
     * @param Core\Request $request
     * @param Core\View $view
     * @param Output $outputHelper
     */
    public function __construct(
        Core\Request $request,
        Core\View $view,
        Core\Helpers\Output $outputHelper
    )
    {
        $this->request = $request;
        $this->view = $view;
        $this->outputHelper = $outputHelper;
    }

    /**
     * Displays a confirm box
     *
     * @param string $text
     * @param int|string|array $forward
     * @param int|string $backward
     * @param integer $overlay
     *
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

            return 'system/alerts/confirm_box.tpl';
        }
        return '';
    }

    /**
     * Displays a confirm box, where the forward button triggers a form submit using POST
     *
     * @param       $text
     * @param array $data
     * @param       $forward
     * @param int $backward
     *
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

            return 'system/alerts/confirm_box_post.tpl';
        }
        return '';
    }

    /**
     * @param $errors
     */
    protected function _setErrorBoxData($errors)
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

        $this->view->assign(
            'error_box',
            [
                'non_integer_keys' => $hasNonIntegerKeys,
                'errors' => $errors
            ]
        );
    }

    /**
     * Gibt eine Box mit den aufgetretenen Fehlern aus
     *
     * @param string|array $errors
     * @param bool $contentOnly
     * @return string
     */
    public function errorBox($errors, $contentOnly = true)
    {
        if ($this->request->getIsAjax() === true) {
            $contentOnly = true;
        }

        $this->view->assign('CONTENT_ONLY', $contentOnly);
        $content = $this->view->fetchTemplate($this->errorBoxContent($errors));

        if ($this->request->getIsAjax() === true) {
            $return = array(
                'success' => false,
                'content' => $content,
            );

            $this->outputHelper->outputJson($return);
        }
        return $content;
    }

    /**
     * @param $errors
     * @return string
     */
    public function errorBoxContent($errors)
    {
        $this->_setErrorBoxData($errors);

        return 'system/alerts/error_box.tpl';
    }

}