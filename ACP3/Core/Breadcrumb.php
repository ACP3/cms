<?php
namespace ACP3\Core;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use Symfony\Component\DependencyInjection\Container;

/**
 * Generates the breadcrumb and page title
 * @package ACP3\Core
 */
class Breadcrumb
{
    /**
     * @var array
     */
    protected $steps = [];

    /**
     * @var array
     */
    protected $title = [
        'separator' => '-',
        'prefix' => '',
        'postfix' => ''
    ];

    /**
     * @var array
     */
    protected $breadcrumbCache = [];

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;

    /**
     * @param \Symfony\Component\DependencyInjection\Container $container
     * @param \ACP3\Core\I18n\Translator                       $translator
     * @param \ACP3\Core\Http\RequestInterface                 $request
     * @param \ACP3\Core\Router                                $router
     * @param \ACP3\Core\Config                                $config
     */
    public function __construct(
        Container $container,
        Translator $translator,
        RequestInterface $request,
        Router $router,
        Config $config
    )
    {
        $this->container = $container;
        $this->translator = $translator;
        $this->request = $request;
        $this->router = $router;
        $this->config = $config;
    }

    /**
     *
     * @param string $value
     *
     * @return $this
     */
    public function setTitleSeparator($value)
    {
        $this->title['separator'] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleSeparator()
    {
        return ' ' . $this->title['separator'] . ' ';
    }

    /**
     *
     * @param string $value
     *
     * @return $this
     */
    public function setTitlePrefix($value)
    {
        $this->title['prefix'] = $value;

        return $this;
    }

    /**
     * Ersetzt die aktuell letzte Brotkrume mit neuen Werten
     *
     * @param string $title
     *    Bezeichnung der jeweiligen Stufe der Brotkrume
     * @param string $path
     *    Die zum $title zugehörige ACP3-interne URI
     * @param bool   $dbSteps
     *
     * @return $this
     */
    public function replaceAncestor($title, $path = '', $dbSteps = false)
    {
        if ($dbSteps === false) {
            $index = count($this->steps) - (!empty($this->steps) ? 1 : 0);
            $this->steps[$index]['title'] = $title;
            $this->steps[$index]['uri'] = !empty($path) ? $this->router->route($path) : '';
        }

        return $this;
    }

    /**
     * Returns the breadcrumb
     *
     * @return array
     */
    public function getBreadcrumb()
    {
        if (empty($this->breadcrumbCache)) {
            $this->_setBreadcrumbCache();
        }

        return $this->breadcrumbCache;
    }

    /**
     * Returns the site title
     *
     * @return string
     */
    public function getSiteTitle()
    {
        return $this->config->getSettings('seo')['title'];
    }

    /**
     * Returns the title of the current page
     *
     * @return string
     */
    public function getPageTitle()
    {
        if (empty($this->breadcrumbCache)) {
            $this->_setBreadcrumbCache();
        }

        return $this->breadcrumbCache[count($this->breadcrumbCache) - 1]['title'];
    }

    /**
     * Returns the title of the current page + the site title
     *
     * @return string
     */
    public function getSiteAndPageTitle()
    {
        $title = $this->getPageTitle();

        $separator = ' ' . $this->title['separator'] . ' ';
        if (!empty($this->title['prefix'])) {
            $title = $this->title['prefix'] . $separator . $title;
        }
        if (!empty($this->title['postfix'])) {
            $title .= $separator . $this->title['postfix'];
        }
        $title .= ' | ' . $this->getSiteTitle();

        return $title;
    }

    /**
     * Sets the breadcrumb cache for the current request
     */
    private function _setBreadcrumbCache()
    {
        // Breadcrumb of the admin panel
        if ($this->request->getArea() === 'admin') {
            $this->setBreadcrumbCacheForAdmin();
        } else { // Breadcrumb for frontend requests
            $this->setBreadcrumbCacheForFrontend();
        }

        // Mark the last breadcrumb
        $this->breadcrumbCache[count($this->breadcrumbCache) - 1]['last'] = true;
    }

    /**
     * Sets the breadcrumb steps cache for admin panel action requests
     */
    private function setBreadcrumbCacheForAdmin()
    {
        if ($this->request->getModule() !== 'acp') {
            // An postfix for the page title has been already set
            if (!empty($this->title['postfix'])) {
                $this->setTitlePostfix($this->title['postfix'] . $this->getTitleSeparator() . $this->translator->t('system',
                        'acp'));
            } else {
                $this->setTitlePostfix($this->translator->t('system', 'acp'));
            }
        }

        // No breadcrumb has been set yet
        if (empty($this->steps)) {
            $this->append($this->translator->t('system', 'acp'), 'acp/acp');

            if ($this->request->getModule() !== 'acp') {
                $this->append(
                    $this->translator->t($this->request->getModule(), $this->request->getModule()),
                    'acp/' . $this->request->getModule()
                );

                $this->setControllerActionBreadcrumbs();
            }
        } else { // Prepend breadcrumb steps, if there have been already some steps set
            if ($this->request->getModule() !== 'acp') {
                $this->prepend(
                    $this->translator->t($this->request->getModule(), $this->request->getModule()),
                    'acp/' . $this->request->getModule()
                );
            }

            $this->prepend($this->translator->t('system', 'acp'), 'acp/acp');
        }
        $this->breadcrumbCache = $this->steps;
    }

    /**
     *
     * @param string $value
     *
     * @return $this
     */
    public function setTitlePostfix($value)
    {
        $this->title['postfix'] = $value;

        return $this;
    }

    /**
     * Zuweisung einer neuen Stufe zur Brotkrümelspur
     *
     * @param string $title
     *    Bezeichnung der jeweiligen Stufe der Brotkrume
     * @param string $path
     *    Die zum $title zugehörige ACP3-interne URI
     *
     * @return $this
     */
    public function append($title, $path = '')
    {
        $this->steps[] = [
            'title' => $title,
            'uri' => !empty($path) ? $this->router->route($path) : ''
        ];

        return $this;
    }

    /**
     * Fügt Brotkrumen an den Anfang an
     *
     * @param string $title
     *    Bezeichnung der jeweiligen Stufe der Brotkrume
     * @param string $path
     *    Die zum $title zugehörige ACP3-interne URI
     *
     * @return $this
     */
    protected function prepend($title, $path)
    {
        $step = [
            'title' => $title,
            'uri' => $this->router->route($path)
        ];
        array_unshift($this->steps, $step);
        return $this;
    }

    /**
     * Sets the breadcrumb steps cache for frontend action requests
     */
    protected function setBreadcrumbCacheForFrontend()
    {
        // No breadcrumb has been set yet
        if (empty($this->steps)) {
            if ($this->request->getModule() !== 'errors') {
                $this->append(
                    $this->translator->t($this->request->getModule(), $this->request->getModule()),
                    $this->request->getModule()
                );
            }

            $this->setControllerActionBreadcrumbs();
        }

        $this->breadcrumbCache = $this->steps;
    }

    private function setControllerActionBreadcrumbs()
    {
        $serviceId = $this->request->getModule() . '.controller.' . $this->request->getArea() . '.' . $this->request->getController();
        if ($this->request->getController() !== 'index' &&
            method_exists($this->container->get($serviceId), 'actionIndex')
        ) {
            $this->append(
                $this->translator->t($this->request->getModule(), $this->getControllerIndexActionTitle()),
                $this->request->getModuleAndController()
            );
        }
        if ($this->request->getControllerAction() !== 'index') {
            $this->append(
                $this->translator->t($this->request->getModule(), $this->getControllerActionTitle()),
                $this->request->getFullPath()
            );
        }
    }

    /**
     * @return string
     */
    private function getControllerActionTitle()
    {
        return $this->request->getArea() . '_' . $this->request->getController() . '_' . $this->request->getControllerAction();
    }

    /**
     * @return string
     */
    private function getControllerIndexActionTitle()
    {
        return $this->request->getArea() . '_' . $this->request->getController() . '_index';
    }
}
