<?php
namespace ACP3\Installer\Core\Modules\Controller;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Installer\Core\Http\Request;
use ACP3\Installer\Core\I18n\Translator;
use ACP3\Installer\Core\Router;

/**
 * Class Context
 * @package ACP3\Installer\Core\Modules\Controller
 */
class Context
{
    /**
     * @var \ACP3\Installer\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Installer\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;

    /**
     * @param \ACP3\Installer\Core\I18n\Translator   $translator
     * @param \ACP3\Core\Http\RequestInterface       $request
     * @param \ACP3\Installer\Core\Router            $router
     * @param \ACP3\Core\View                        $view
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(
        Translator $translator,
        RequestInterface $request,
        Router $router,
        \ACP3\Core\View $view,
        ApplicationPath $appPath
    )
    {
        $this->translator = $translator;
        $this->request = $request;
        $this->router = $router;
        $this->view = $view;
        $this->appPath = $appPath;
    }

    /**
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return \ACP3\Core\View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return \ACP3\Core\Environment\ApplicationPath
     */
    public function getAppPath()
    {
        return $this->appPath;
    }
}
