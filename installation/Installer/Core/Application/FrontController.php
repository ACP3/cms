<?php
namespace ACP3\Installer\Core\Application;

use ACP3\Core\Exceptions;
use ACP3\Core\Http\RequestInterface;

/**
 * Class FrontController
 * @package ACP3\Installer\Core\Application
 */
class FrontController extends \ACP3\Core\Application\FrontController
{
    /**
     * @inheritdoc
     */
    protected function checkForUriAlias(RequestInterface $request)
    {
    }
}
