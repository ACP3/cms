<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Controller\Frontend\Index;

use ACP3\Core;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Image extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $sessionHandler;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Session $sessionHandler
    ) {
        parent::__construct($context);

        $this->sessionHandler = $sessionHandler;
    }

    /**
     * @throws \Exception
     */
    public function execute(string $path): Response
    {
        $this->response->headers->set('Content-type', 'image/gif');
        $this->response->headers->addCacheControlDirective('no-cache', true);
        $this->response->headers->addCacheControlDirective('must-revalidate', true);
        $this->response->headers->add(['Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT']);

        if ($this->sessionHandler->has('captcha_' . $path)) {
            $this->generateCaptcha($this->sessionHandler->get('captcha_' . $path));
        }

        return $this->response;
    }

    /**
     * @throws \Exception
     */
    protected function generateCaptcha(string $captchaText): void
    {
        $captchaLength = \strlen($captchaText);
        $width = $captchaLength * 25;
        $height = 30;

        \ob_start();

        $image = \imagecreate($width, $height);

        // Background color
        \imagecolorallocate($image, 255, 255, 255);

        $textColor = \imagecolorallocate($image, 0, 0, 0);

        for ($i = 0; $i < $captchaLength; ++$i) {
            $font = \random_int(2, 5);
            $posLeft = 22 * $i + 10;
            $posTop = \random_int(1, $height - \imagefontheight($font) - 3);
            \imagestring($image, $font, $posLeft, $posTop, $captchaText[$i], $textColor);
        }

        \imagegif($image);
        \imagedestroy($image);

        $this->response->setContent(\ob_get_clean());
    }
}
