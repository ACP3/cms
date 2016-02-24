<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Controller\Index;

use ACP3\Core;

/**
 * Class Image
 * @package ACP3\Modules\ACP3\Captcha\Controller\Index
 */
class Image extends Core\Controller\FrontendController
{
    /**
     * @var \ACP3\Core\SessionHandler
     */
    protected $sessionHandler;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\SessionHandler                     $sessionHandler
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\SessionHandler $sessionHandler
    )
    {
        parent::__construct($context);

        $this->sessionHandler = $sessionHandler;
    }

    /**
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function execute($path)
    {
        $this->setContentType('image/gif');
        $this->response->headers->addCacheControlDirective('no-cache', true);
        $this->response->headers->addCacheControlDirective('must-revalidate', true);
        $this->response->headers->add(['Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT']);
        $this->response->sendHeaders();

        if ($this->sessionHandler->has('captcha_' . $path)) {
            $captcha = $this->sessionHandler->get('captcha_' . $path);
            $captchaLength = strlen($captcha);
            $width = $captchaLength * 25;
            $height = 30;

            $im = imagecreate($width, $height);

            // Background color
            imagecolorallocate($im, 255, 255, 255);

            $textColor = imagecolorallocate($im, 0, 0, 0);

            for ($i = 0; $i < $captchaLength; ++$i) {
                $font = mt_rand(2, 5);
                $posLeft = 22 * $i + 10;
                $posTop = mt_rand(1, $height - imagefontheight($font) - 3);
                imagestring($im, $font, $posLeft, $posTop, $captcha[$i], $textColor);
            }
            imagegif($im);
            imagedestroy($im);

        }

        return $this->response;
    }
}
