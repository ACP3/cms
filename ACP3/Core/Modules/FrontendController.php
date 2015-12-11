<?php

namespace ACP3\Core\Modules;

use ACP3\Core;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FrontendController
 * @package ACP3\Core\Modules
 */
abstract class FrontendController extends Core\Modules\Controller
{
    /**
     * @var \ACP3\Core\Assets
     */
    protected $assets;
    /**
     * @var \ACP3\Core\Breadcrumb
     */
    protected $breadcrumb;
    /**
     * @var \ACP3\Core\SEO
     */
    protected $seo;
    /**
     * @var Core\Helpers\RedirectMessages
     */
    protected $redirectMessages;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    protected $actionHelper;
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;

    /**
     * @var string
     */
    private $contentType = 'text/html';
    /**
     * @var string
     */
    private $charset = "UTF-8";

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $frontendContext
     */
    public function __construct(Controller\FrontendContext $frontendContext)
    {
        parent::__construct($frontendContext);

        $this->assets = $frontendContext->getAssets();
        $this->breadcrumb = $frontendContext->getBreadcrumb();
        $this->seo = $frontendContext->getSeo();
        $this->actionHelper = $frontendContext->getActionHelper();
        $this->response = $frontendContext->getResponse();
    }

    /**
     * Helper function for initializing models, etc.
     */
    public function preDispatch()
    {
        $path = $this->request->getArea() . '/' . $this->request->getFullPathWithoutArea();

        if ($this->acl->hasPermission($path) === false) {
            throw new Core\Exceptions\AccessForbidden();
        }

        // Get the current resultset position
        if (!defined('POS')) {
            define('POS', (int)$this->request->getParameters()->get('page') >= 1 ? (int)($this->request->getParameters()->get('page') - 1) * $this->user->getEntriesPerPage() : 0);
        }

        $this->view->assign('PHP_SELF', PHP_SELF);
        $this->view->assign('REQUEST_URI', $this->request->getServer()->get('REQUEST_URI'));
        $this->view->assign('ROOT_DIR', ROOT_DIR);
        $this->view->assign('HOST_NAME', $this->request->getDomain());
        $this->view->assign('ROOT_DIR_ABSOLUTE', $this->request->getDomain() . ROOT_DIR);
        $this->view->assign('DESIGN_PATH', DESIGN_PATH);
        $this->view->assign('DESIGN_PATH_ABSOLUTE', DESIGN_PATH_ABSOLUTE);
        $this->view->assign('UA_IS_MOBILE', $this->request->getUserAgent()->isMobileBrowser());
        $this->view->assign('IN_ADM', $this->request->getArea() === 'admin');
        $this->view->assign('IS_HOMEPAGE', $this->request->isHomepage());
        $this->view->assign('IS_AJAX', $this->request->isAjax());
        $this->view->assign('LANG_DIRECTION', $this->translator->getDirection());
        $this->view->assign('LANG', $this->translator->getShortIsoCode());

        return parent::preDispatch();
    }

    /**
     * @param callable    $callback
     * @param null|string $path
     */
    protected function handlePostAction(callable $callback, $path = null)
    {
        try {
            $callback();
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), $path);
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * Outputs the requested module controller action
     *
     * @param $controllerActionResult
     */
    public function display($controllerActionResult)
    {
        if ($controllerActionResult instanceof Response) {
            $controllerActionResult->send();
            return;
        } else if (is_array($controllerActionResult)) {
            $this->view->assign($controllerActionResult);
        } else if (is_string($controllerActionResult)) {
            echo $controllerActionResult;
            return;
        }

        // Output content through the controller
        $this->response->headers->set('Content-Type', $this->getContentType());
        $this->response->setCharset($this->getCharset());

        if (!$this->getContent()) {
            // Set the template automatically
            if ($this->getTemplate() === '') {
                $this->setTemplate($this->request->getModule() . '/' . ucfirst($this->request->getArea()) . '/' . $this->request->getController() . '.' . $this->request->getControllerAction() . '.tpl');
            }

            $this->view->assign('BREADCRUMB', $this->breadcrumb->getBreadcrumb());
            $this->view->assign('META', $this->seo->getMetaTags());

            $this->response->setContent($this->view->fetchTemplate($this->getTemplate()));
        }

        $this->response->send();
    }

    /**
     * Gibt den Content-Type der anzuzeigenden Seiten zurück
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Weist der aktuell auszugebenden Seite den Content-Type zu
     *
     * @param string $data
     *
     * @return $this
     */
    public function setContentType($data)
    {
        $this->contentType = $data;

        return $this;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     *
     * @return $this
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * Gibt den auszugebenden Seiteninhalt zurück
     *
     * @return string
     */
    public function getContent()
    {
        return $this->response->getContent();
    }

    /**
     * Weist dem Template den auszugebenden Inhalt zu
     *
     * @param string $data
     *
     * @return $this
     */
    public function setContent($data)
    {
        $this->response->setContent($data);

        return $this;
    }


    /**
     * @return Core\Helpers\RedirectMessages
     */
    public function redirectMessages()
    {
        if (!$this->redirectMessages) {
            $this->redirectMessages = $this->get('core.helpers.redirect');
        }

        return $this->redirectMessages;
    }

    /**
     * @return Core\Redirect
     */
    public function redirect()
    {
        return $this->get('core.redirect');
    }
}
