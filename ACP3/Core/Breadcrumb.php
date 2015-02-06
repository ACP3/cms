<?php
namespace ACP3\Core;

use ACP3\Modules\Menus;

/**
 * Generates the breadcrumb and page title
 * @package ACP3\Core
 */
class Breadcrumb
{
    /**
     * Enthält alle Schritte der Brotkrümelspur,
     * welche sich aus der Navigationsstruktur der Website ergeben
     *
     * @var array
     */
    protected $stepsFromDb = [];
    /**
     * Enthält alle Schritte der Brotkrümelspur,
     * welche von den Modulen festgelegt werden
     *
     * @var array
     */
    protected $stepsFromModules = [];

    /**
     * @var array
     */
    protected $title = [
        'separator' => '-',
        'prefix' => '',
        'postfix' => ''
    ];

    /**
     * Enthält die gecachete Brotkrümelspur
     *
     * @var array
     */
    protected $breadcrumbCache = [];

    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Request
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\Config
     */
    protected $seoConfig;
    /**
     * @var \ACP3\Modules\Menus\Model
     */
    protected $menusModel;

    /**
     * @param \ACP3\Core\Lang    $lang
     * @param \ACP3\Core\Request $request
     * @param \ACP3\Core\Router  $router
     * @param \ACP3\Core\Config  $config
     */
    public function __construct(
        Lang $lang,
        Request $request,
        Router $router,
        Config $config
    )
    {
        $this->lang = $lang;
        $this->request = $request;
        $this->router = $router;
        $this->seoConfig = $config;
    }

    /**
     * @param \ACP3\Modules\Menus\Model $menusModel
     *
     * @return $this
     */
    public function setMenusModel(Menus\Model $menusModel)
    {
        $this->menusModel = $menusModel;

        return $this;
    }

    /**
     * Initializes and pre populates the breadcrumb
     */
    public function prePopulate()
    {
        if ($this->request->area !== 'admin' && $this->menusModel) {
            $in = [
                $this->request->query,
                $this->request->getUriWithoutPages(),
                $this->request->mod . '/' . $this->request->controller . '/' . $this->request->file . '/',
                $this->request->mod . '/' . $this->request->controller . '/',
                $this->request->mod
            ];
            $items = $this->menusModel->getMenuItemsByUri($in);
            $c_items = count($items);

            // Populate the breadcrumb with internal pages
            for ($i = 0; $i < $c_items; ++$i) {
                $this->_appendFromDB($items[$i]['title'], $items[$i]['uri']);
            }
        }
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
    protected function _appendFromDB($title, $path = '')
    {
        $this->stepsFromDb[] = [
            'title' => $title,
            'uri' => !empty($path) ? $this->router->route($path) : ''
        ];

        return $this;
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
        if ($dbSteps === true) {
            $index = count($this->stepsFromDb) - (!empty($this->stepsFromDb) ? 1 : 0);
            $this->stepsFromDb[$index]['title'] = $title;
            $this->stepsFromDb[$index]['uri'] = !empty($path) ? $this->router->route($path) : '';
        } else {
            $index = count($this->stepsFromModules) - (!empty($this->stepsFromModules) ? 1 : 0);
            $this->stepsFromModules[$index]['title'] = $title;
            $this->stepsFromModules[$index]['uri'] = !empty($path) ? $this->router->route($path) : '';
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
        return $this->seoConfig->getSettings('seo')['title'];
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
        if ($this->request->area === 'admin') {
            $this->_setBreadcrumbCacheForAdmin();
        } else { // Breadcrumb for frontend requests
            $this->_setBreadcrumbCacheForFrontend();
        }

        // Mark the last breadcrumb
        $this->breadcrumbCache[count($this->breadcrumbCache) - 1]['last'] = true;
    }

    /**
     * Sets the breadcrumb steps cache for admin panel action requests
     */
    private function _setBreadcrumbCacheForAdmin()
    {
        $module = $this->request->mod;

        if ($module !== 'acp') {
            // An postfix for the page title has been already set
            if (!empty($this->title['postfix'])) {
                $this->setTitlePostfix($this->title['postfix'] . $this->getTitleSeparator() . $this->lang->t('system', 'acp'));
            } else {
                $this->setTitlePostfix($this->lang->t('system', 'acp'));
            }
        }

        // No breadcrumb is set yet
        if (empty($this->stepsFromModules)) {
            $controller = $this->request->controller;
            $file = $this->request->file;
            $languageKey = $this->request->area . '_' . $controller . '_' . $file;
            $languageKeyIndex = $this->request->area . '_' . $controller . '_index';

            $this->append($this->lang->t('system', 'acp'), 'acp/acp');

            if ($module !== 'acp') {
                $this->append($this->lang->t($module, $module), 'acp/' . $module);

                if ($controller !== 'index' &&
                    method_exists("\\ACP3\\Modules\\" . ucfirst($module) . "\\Controller\\Admin\\" . ucfirst($controller), 'actionIndex')
                ) {
                    $this->append($this->lang->t($module, $languageKeyIndex), 'acp/' . $module . '/' . $controller);
                }
                if ($file !== 'index') {
                    $this->append($this->lang->t($module, $languageKey), 'acp/' . $module . '/' . $controller . '/' . $file);
                }
            }
        } else { // Prepend breadcrumb steps, if there have been already some steps set
            if ($module !== 'acp') {
                $this->prepend($this->lang->t($module, $module), 'acp/' . $module);
            }
            $this->prepend($this->lang->t('system', 'acp'), 'acp/acp');
        }
        $this->breadcrumbCache = $this->stepsFromModules;
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
        $this->stepsFromModules[] = [
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
        array_unshift($this->stepsFromModules, $step);
        return $this;
    }

    /**
     * Sets the breadcrumb steps cache for frontend action requests
     */
    private function _setBreadcrumbCacheForFrontend()
    {
        // No breadcrumb has been set yet
        if (empty($this->stepsFromModules)) {
            $module = $this->request->mod;
            $controller = $this->request->controller;
            $file = $this->request->file;
            $languageKey = $this->request->area . '_' . $controller . '_' . $file;
            $languageKeyIndex = $this->request->area . '_' . $controller . '_index';

            if ($module !== 'errors') {
                $this->append($this->lang->t($module, $module), $module);
            }
            if ($controller !== 'index' &&
                method_exists("\\ACP3\\Modules\\" . ucfirst($module) . "\\Controller\\" . ucfirst($controller), 'actionIndex')
            ) {
                $this->append($this->lang->t($module, $languageKeyIndex), $module . '/' . $controller);
            }
            if ($file !== 'index') {
                $this->append($this->lang->t($module, $languageKey), 'acp/' . $module . '/' . $controller . '/' . $file);
            }
        }

        if (!empty($this->stepsFromModules) && empty($this->stepsFromDb)) {
            $this->breadcrumbCache = $this->stepsFromModules;
        } else {
            $this->breadcrumbCache = $this->stepsFromDb;

            if ($this->breadcrumbCache[count($this->breadcrumbCache) - 1]['uri'] === $this->stepsFromModules[0]['uri']) {
                $c_stepsFromModules = count($this->stepsFromModules);
                for ($i = 1; $i < $c_stepsFromModules; ++$i) {
                    $this->breadcrumbCache[] = $this->stepsFromModules[$i];
                }
            }
        }
    }
}
