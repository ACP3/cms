<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core;

class Alerts
{
    public function __construct(private readonly Core\Http\RequestInterface $request, private readonly Core\View $view)
    {
    }

    /**
     * Allows to display a confirmation box.
     * The method's return value is the rendered confirmation box.
     *
     * @param array{url: string, lang: string}|string $forward
     */
    public function confirmBox(string $text, array|string $forward = '', string $backward = '', bool $overlay = false): string
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
     *
     * @param array<mixed, mixed> $data
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
     * @param array<string, string>|string $errors
     */
    public function errorBox(array|string $errors): string
    {
        $this->setErrorBoxData($errors);

        return $this->view->fetchTemplate('System/Alerts/error_box.tpl');
    }

    /**
     * @param array<string, string>|string $errors
     */
    protected function setErrorBoxData(array|string $errors): void
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
     * @param array<string, string>|string $errors
     *
     * @return array<string, string>
     */
    protected function prepareErrorBoxData(array|string $errors): array
    {
        if (\is_string($errors) && ($data = @unserialize($errors, ['allowed_classes' => true])) !== false) {
            $errors = $data;
        }

        if (\is_array($errors) === false) {
            $errors = [$errors];
        }

        return $errors;
    }
}
