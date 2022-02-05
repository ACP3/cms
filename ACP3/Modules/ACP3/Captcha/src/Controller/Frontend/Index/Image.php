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
    public function __construct(
        Core\Controller\Context\Context $context,
        protected Session $sessionHandler
    ) {
        parent::__construct($context);
    }

    /**
     * @throws \Exception
     */
    public function __invoke(string $token): Response
    {
        $response = new Response();
        $response->headers->set('Content-type', 'text/plain');
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->add(['Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT']);

        if ($this->sessionHandler->has('captcha_' . $token)) {
            $response->setContent($this->generateCaptcha($this->sessionHandler->get('captcha_' . $token)));
        } else {
            $response->setContent($this->generateCaptcha('invalid captcha!', true));
        }

        return $response;
    }

    /**
     * @throws \Exception
     */
    protected function generateCaptcha(string $captchaText, bool $renderAsError = false): string
    {
        $captchaLength = mb_strlen($captchaText);
        $width = $captchaLength * 25;
        $height = 30;

        ob_start();

        $image = imagecreate($width, $height);

        // Background color
        imagecolorallocate($image, 255, 255, 255);

        $textColor = imagecolorallocate($image, 0, 0, 0);

        for ($i = 0; $i < $captchaLength; ++$i) {
            $font = $renderAsError ? 5 : random_int(2, 5);
            $posLeft = 22 * $i + 10;
            $posTop = $renderAsError ? $height - imagefontheight($font) - 3 : random_int(1, $height - imagefontheight($font) - 3);
            imagestring($image, $font, $posLeft, $posTop, $captchaText[$i], $textColor);
        }

        imagegif($image);
        imagedestroy($image);

        return 'data:image/gif;base64,' . base64_encode(ob_get_clean());
    }
}
