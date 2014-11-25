<?php
namespace ACP3\Core;

/**
 * Generates the breadcrumb and page title
 *
 * @author Tino Goratsch
 */
class Breadcrumb
{
    /**
     * Enthält alle Schritte der Brotkrümelspur,
     * welche sich aus der Navigationsstruktur der Website ergeben
     *
     * @var array
     * @access private
     */
    protected $stepsFromDb = [];
    /**
     * Enthält alle Schritte der Brotkrümelspur,
     * welche von den Modulen festgelegt werden
     *
     * @var array
     * @access private
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
     * @var Lang
     */
    protected $lang;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var array
     */
    protected $systemConfig = [];

    public function __construct(
        DB $db,
        Lang $lang,
        Request $request,
        Router $router,
        Config $systemConfig
    ) {
        $this->lang = $lang;
        $this->request = $request;
        $this->router = $router;
        $this->systemConfig = $systemConfig->getSettings();

        // Frontendbereich
        if ($request->area !== 'admin') {
            $in = [
                $request->query,
                $request->getUriWithoutPages(),
                $request->mod . '/' . $request->controller . '/' . $request->file . '/',
                $request->mod . '/' . $request->controller . '/',
                $request->mod
            ];
            $items = $db->getConnection()->executeQuery('SELECT p.title, p.uri, p.left_id, p.right_id FROM ' . $db->getPrefix() . 'menu_items AS c, ' . $db->getPrefix() . 'menu_items AS p WHERE c.left_id BETWEEN p.left_id AND p.right_id AND c.uri IN(?) GROUP BY p.uri ORDER BY p.left_id ASC', [$in], [\Doctrine\DBAL\Connection::PARAM_STR_ARRAY])->fetchAll();
            $c_items = count($items);

            // Dynamische Seite (ACP3 intern)
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
    public function replaceAnchestor($title, $path = '', $dbSteps = false)
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
     * Gibt je nach Modus entweder die Brotkrümelspur oder den Seitentitel aus
     *
     * @param int $mode
     *  1 = Brotkrümelspur ausgeben
     *  2 = Nur Seitentitel ausgeben
     *  3 = Seitentitel mit eventuellen Prefixes und Postfixes ausgeben
     *
     * @return string
     */
    public function output($mode = 1)
    {
        if (empty($this->breadcrumbCache)) {
            $this->_setBreadcrumbCache();
        }

        // Just return the breadcrumb
        if ($mode === 1) {
            return $this->breadcrumbCache;
        } else { // Just return the title
            // The last index of the breadcrumb is the page title
            $title = $this->breadcrumbCache[count($this->breadcrumbCache) - 1]['title'];
            if ($mode === 3) {
                $separator = ' ' . $this->title['separator'] . ' ';
                if (!empty($this->title['prefix'])) {
                    $title = $this->title['prefix'] . $separator . $title;
                }
                if (!empty($this->title['postfix'])) {
                    $title .= $separator . $this->title['postfix'];
                }
                $title .= ' | ' . $this->systemConfig['seo_title'];
            }
            return $title;
        }
    }

    /**
     * Sets the breadcrumb cache for the current request
     */
    private function _setBreadcrumbCache()
    {
        // Brotkrümelspur für das Admin-Panel
        if ($this->request->area === 'admin') {
            $this->_setBreadcrumbCacheForAdmin();
        } else { // Breadcrumb for frontend requests
            $this->_setBreadcrumbCacheForFrontend();
        }

        // Letzte Brotkrume markieren
        $this->breadcrumbCache[count($this->breadcrumbCache) - 1]['last'] = true;
    }

    /**
     * Sets the breadcrumb steps cache for admin panel action requests
     */
    private function _setBreadcrumbCacheForAdmin()
    {
        $module = $this->request->mod;

        if ($module !== 'acp') {
            $this->setTitlePostfix($this->lang->t('system', 'acp'));
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
