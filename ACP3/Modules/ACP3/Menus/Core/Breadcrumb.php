<?php
namespace ACP3\Modules\ACP3\Menus\Core;

use ACP3\Core;
use ACP3\Core\Http\RequestInterface;
use ACP3\Modules\ACP3\Menus;
use Symfony\Component\DependencyInjection\Container;

/**
 * Generates the breadcrumb and page title
 * @package ACP3\Modules\ACP3\Menus\Core
 */
class Breadcrumb extends Core\Breadcrumb
{
    /**
     * Enthält alle Schritte der Brotkrümelspur,
     * welche sich aus der Navigationsstruktur der Website ergeben
     *
     * @var array
     */
    protected $stepsFromDb = [];

    /**
     * @var \ACP3\Modules\ACP3\Menus\Model
     */
    protected $menusModel;

    /**
     * @param \Symfony\Component\DependencyInjection\Container $container
     * @param \ACP3\Core\Lang                                  $lang
     * @param \ACP3\Core\Http\RequestInterface                 $request
     * @param \ACP3\Core\Router                                $router
     * @param \ACP3\Core\Config                                $config
     * @param \ACP3\Modules\ACP3\Menus\Model                   $menusModel
     */
    public function __construct(
        Container $container,
        Core\Lang $lang,
        RequestInterface $request,
        Core\Router $router,
        Core\Config $config,
        Menus\Model $menusModel
    )
    {
        parent::__construct($container, $lang, $request, $router, $config);

        $this->menusModel = $menusModel;
    }

    /**
     * Initializes and pre populates the breadcrumb
     */
    public function prePopulate()
    {
        parent::prePopulate();

        if ($this->request->getArea() !== 'admin') {
            $in = [
                $this->request->getQuery(),
                $this->request->getUriWithoutPages(),
                $this->request->getFullPath(),
                $this->request->getModuleAndController(),
                $this->request->getModule()
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
        }

        return parent::replaceAncestor($title, $path, $dbSteps);
    }

    /**
     * Sets the breadcrumb steps cache for frontend action requests
     */
    protected function setBreadcrumbCacheForFrontend()
    {
        parent::setBreadcrumbCacheForFrontend();

        if (!empty($this->steps) && empty($this->stepsFromDb)) {
            $this->breadcrumbCache = $this->steps;
        } else {
            $this->breadcrumbCache = $this->stepsFromDb;

            if ($this->breadcrumbCache[count($this->breadcrumbCache) - 1]['uri'] === $this->steps[0]['uri']) {
                $c_stepsFromModules = count($this->steps);
                for ($i = 1; $i < $c_stepsFromModules; ++$i) {
                    $this->breadcrumbCache[] = $this->steps[$i];
                }
            }
        }
    }
}
