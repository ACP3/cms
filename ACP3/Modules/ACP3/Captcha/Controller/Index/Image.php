<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Controller\Index;

use ACP3\Core;

/**
 * Class Image
 * @package ACP3\Modules\ACP3\Captcha\Controller\Index
 */
class Image extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Session\SessionHandlerInterface
     */
    protected $sessionHandler;

    /**
     * Image constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Session\SessionHandlerInterface    $sessionHandler
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Session\SessionHandlerInterface $sessionHandler
    ) {
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
     * @param $captchaText
     */
    protected function generateCaptcha($captchaText)
    {
        $captchaLength = strlen($captchaText);
        $width = $captchaLength * 25;
        $height = 30;

        ob_start();

        $image = imagecreate($width, $height);

        // Background color
        imagecolorallocate($image, 255, 255, 255);

        $textColor = imagecolorallocate($image, 0, 0, 0);

        for ($i = 0; $i < $captchaLength; ++$i) {
            $font = mt_rand(2, 5);
            $posLeft = 22 * $i + 10;
            $posTop = mt_rand(1, $height - imagefontheight($font) - 3);
            imagestring($image, $font, $posLeft, $posTop, $captchaText[$i], $textColor);
        }

        imagegif($image);
        imagedestroy($image);

        $this->response->setContent(ob_get_contents());

        ob_end_clean();
    }
}
