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
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;

    /**
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\View                  $view
     */
    public function __construct(
        Core\Http\RequestInterface $request,
        Core\View $view
    ) {
        $this->request = $request;
        $this->view = $view;
    }

    /**
     * Displays a confirm box
     *
     * @param string       $text
     * @param string|array $forward
     * @param string       $backward
     * @param integer      $overlay
     *
     * @return string
     */
    public function confirmBox($text, $forward = '', $backward = '', $overlay = 0)
    {
        if (!empty($text)) {
            $confirm = [
                'text' => $text,
                'forward' => $forward,
                'overlay' => $overlay,
            ];
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
     * @param string $text
     * @param array  $data
     * @param string $forward
     * @param string $backward
     *
     * @return string
     */
    public function confirmBoxPost($text, array $data, $forward, $backward = '')
    {
        if (!empty($text) && !empty($data)) {
            $confirm = [
                'text' => $text,
                'data' => $data,
                'forward' => $forward,
            ];
            if (!empty($backward)) {
                $confirm['backward'] = $backward;
            }

            $this->view->assign('confirm', $confirm);

            return 'system/alerts/confirm_box_post.tpl';
        }
        return '';
    }

    /**
     * @param string|array $errors
     */
    protected function setErrorBoxData($errors)
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
     * @param bool         $contentOnly
     *
     * @return string
     */
    public function errorBox($errors, $contentOnly = true)
    {
        if ($this->request->isAjax() === true) {
            $contentOnly = true;
        }

        $this->view->assign('CONTENT_ONLY', $contentOnly);
        return $this->view->fetchTemplate($this->errorBoxContent($errors));
    }

    /**
     * @param string|array $errors
     *
     * @return string
     */
    public function errorBoxContent($errors)
    {
        $this->setErrorBoxData($errors);

        return 'system/alerts/error_box.tpl';
    }
}
