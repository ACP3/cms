<?php
namespace ACP3\Installer\Core;

use ACP3\Core\Exceptions;
use ACP3\Core\Http\RequestInterface;

/**
 * Class FrontController
 * @package ACP3\Installer\Core
 */
class FrontController extends \ACP3\Core\FrontController
{
    /**
     * @inheritdoc
     */
    protected function checkForUriAlias(RequestInterface $request)
    {
    }
}
