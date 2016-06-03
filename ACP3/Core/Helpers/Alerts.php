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
     * @param \ACP3\Core\View $view
     */
    public function __construct(
        Core\Http\RequestInterface $request,
        Core\View $view
    ) {
        $this->request = $request;
        $this->view = $view;
    }

    /**
     * Displays a confirmation box
     *
     * @param string $text
     * @param string|array $forward
     * @param string $backward
     * @param integer $overlay
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
     * Displays a confirmation box, where the forward button triggers a form submit using POST
     *
     * @param string $text
     * @param array $data
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
     * Returns the pretty printed form errors
     *
     * @param string|array $errors
     * @return string
     */
    public function errorBox($errors)
    {
        $this->view->assign('CONTENT_ONLY', $this->request->isXmlHttpRequest() === true);
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

    /**
     * @param string|array $errors
     */
    protected function setErrorBoxData($errors)
    {
        $hasNonIntegerKeys = false;

        $errors = $this->prepareErrorBoxData($errors);

        foreach (array_keys($errors) as $key) {
            if (is_numeric($key) === false) {
                $hasNonIntegerKeys = true;
                break;
            }
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
     * @param string|array $errors
     * @return array
     */
    protected function prepareErrorBoxData($errors)
    {
        if (is_string($errors) && ($data = @unserialize($errors)) !== false) {
            $errors = $data;
        }

        if (is_array($errors) === false) {
            $errors = (array)$errors;
        }
        
        return $errors;
    }
}
