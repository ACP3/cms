<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core;

class Alerts
{
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;

    public function __construct(
        Core\Http\RequestInterface $request,
        Core\View $view
    ) {
        $this->request = $request;
        $this->view = $view;
    }

    /**
     * Allows to display a confirmation box.
     * The method's return value is the rendered confirmation box.
     *
     * @param string|array $forward
     */
    public function confirmBox(string $text, $forward = '', string $backward = '', bool $overlay = false): string
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

            return $this->view->fetchTemplate('System/Alerts/confirm_box.tpl');
        }

        throw new \InvalidArgumentException('To display the confirmation box, you must supply a text you want to display!');
    }

    /**
     * Allows to display a confirmation box, where the forward button triggers a form submit using POST.
     * The method's return value is the rendered confirmation box.
     */
    public function confirmBoxPost(string $text, array $data, string $forward, string $backward = ''): string
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

            return $this->view->fetchTemplate('System/Alerts/confirm_box_post.tpl');
        }

        throw new \InvalidArgumentException('To display the confirmation box, you must supply a text you want to display and the data to be POSTed!');
    }

    /**
     * Returns the pretty printed form errors.
     *
     * @param string|array $errors
     */
    public function errorBox($errors): string
    {
        return $this->view->fetchTemplate($this->errorBoxContent($errors));
    }

    /**
     * @param string|array $errors
     *
     * @deprecated To be removed with ACP3 version 6.0.0. Use the method errorBox() instead.
     */
    public function errorBoxContent($errors): string
    {
        $this->setErrorBoxData($errors);

        return 'System/Alerts/error_box.tpl';
    }

    /**
     * @param string|array $errors
     */
    protected function setErrorBoxData($errors): void
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
                'errors' => $errors,
            ]
        );

        if ($this->request->isXmlHttpRequest() === true) {
            $this->view->setLayout('System/layout.content_only.tpl');
        }
    }

    /**
     * @param string|array $errors
     */
    protected function prepareErrorBoxData($errors): array
    {
        if (\is_string($errors) && ($data = @unserialize($errors, ['allowed_classes' => true])) !== false) {
            $errors = $data;
        }

        if (\is_array($errors) === false) {
            $errors = (array) $errors;
        }

        return $errors;
    }
}
