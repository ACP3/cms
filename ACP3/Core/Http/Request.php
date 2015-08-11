<?php
namespace ACP3\Core\Http;

use ACP3\Core\Config;
use ACP3\Core\Modules;
use ACP3\Core\Http\Request\ParameterBag;
use ACP3\Modules\ACP3\Seo;

/**
 * Class Request
 * @package ACP3\Core\Http
 */
class Request extends AbstractRequest
{
    const ADMIN_PANEL_PATTERN = '=^acp/=';

    /**
     * @var \ACP3\Core\Modules\Helper\ControllerActionExists
     */
    protected $controllerActionExists;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model
     */
    protected $seoModel;

    /**
     * @var string
     */
    protected $area = '';
    /**
     * @var string
     */
    protected $module = '';
    /**
     * @var string
     */
    protected $controller = '';
    /**
     * @var string
     */
    protected $controllerAction = '';
    /**
     * @var \ACP3\Core\Http\Request\ParameterBag
     */
    protected $parameters;
    /**
     * @var string
     */
    protected $query = '';
    /**
     * @var string
     */
    protected $originalQuery = '';
    /**
     * @var bool
     */
    protected $isHomepage;

    /**
     * @param \ACP3\Core\Modules\Helper\ControllerActionExists $controllerActionExists
     * @param \ACP3\Core\Config                                $config
     * @param \ACP3\Modules\ACP3\Seo\Model                     $seoModel
     */
    public function __construct(
        Modules\Helper\ControllerActionExists $controllerActionExists,
        Config $config,
        Seo\Model $seoModel
    )
    {
        $this->controllerActionExists = $controllerActionExists;
        $this->config = $config;
        $this->seoModel = $seoModel;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @inheritdoc
     */
    public function getOriginalQuery()
    {
        return $this->originalQuery;
    }

    /**
     * @inheritdoc
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @inheritdoc
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @inheritdoc
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @inheritdoc
     */
    public function getControllerAction()
    {
        return $this->controllerAction;
    }

    /**
     * @inheritdoc
     */
    public function getFullPath()
    {
        return $this->getModuleAndController() . $this->controllerAction . '/';
    }

    /**
     * @inheritdoc
     */
    public function getFullPathWithoutArea()
    {
        return $this->getModuleAndControllerWithoutArea() . $this->controllerAction . '/';
    }

    /**
     * @inheritdoc
     */
    public function getModuleAndController()
    {
        $path = ($this->area === 'admin') ? 'acp/' : '';
        $path .= $this->getModuleAndControllerWithoutArea();

        return $path;
    }

    /**
     * @inheritdoc
     */
    public function getModuleAndControllerWithoutArea()
    {
        return $this->module . '/' . $this->controller . '/';
    }


    /**
     * Processes the URL of the current request
     */
    public function processQuery()
    {
        $this->setOriginalQuery();

        $this->query = $this->originalQuery;

        // It's an request for the admin panel page
        if (preg_match(self::ADMIN_PANEL_PATTERN, $this->query)) {
            $this->area = 'admin';
            // strip "acp/"
            $this->query = substr($this->query, 4);
        } else {
            $this->area = 'frontend';

            $homepage = $this->config->getSettings('system')['homepage'];

            // Set the user defined homepage of the website
            if ($this->query === '/' && $homepage !== '') {
                $this->query = $homepage;
            }

            $this->_checkForUriAlias();
        }

        $this->parseURI();

        return;
    }

    /**
     * Checks, whether the current request may equals an uri alias
     */
    protected function _checkForUriAlias()
    {
        list($params, $probableQuery) = $this->checkUriAliasForAdditionalParameters();

        // Nachschauen, ob ein URI-Alias für die aktuelle Seite festgelegt wurde
        $alias = $this->seoModel->getUriByAlias(substr($probableQuery, 0, -1));
        if (!empty($alias)) {
            $this->query = $alias . $params;
        }
    }

    /**
     * Setzt alle in URI::query enthaltenen Parameter
     */
    protected function parseURI()
    {
        $query = preg_split('=/=', $this->query, -1, PREG_SPLIT_NO_EMPTY);

        if (isset($query[0])) {
            $this->module = $query[0];
        } else {
            $this->module = ($this->area === 'admin') ? 'acp' : 'news';
        }

        $this->controller = isset($query[1]) ? $query[1] : 'index';
        $this->controllerAction = isset($query[2]) ? $query[2] : 'index';

        $query = $this->completeQuery($query);

        $this->setRequestParameters($query);
    }

    /**
     * @inheritdoc
     */
    public function isHomepage()
    {
        if ($this->isHomepage === null) {
            $this->isHomepage = ($this->query === $this->config->getSettings('system')['homepage']);
        }

        return $this->isHomepage;
    }

    /**
     * @inheritdoc
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @inheritdoc
     */
    public function getUriWithoutPages()
    {
        return preg_replace('/\/page_(\d+)\//', '/', $this->query);
    }

    protected function setOriginalQuery()
    {
        $this->originalQuery = substr(str_replace(PHP_SELF, '', htmlentities($this->getServer()->get('PHP_SELF', ''), ENT_QUOTES)), 1);
        $this->originalQuery .= !preg_match('/\/$/', $this->originalQuery) ? '/' : '';
    }

    /**
     * @param $query
     */
    protected function setRequestParameters($query)
    {
        $this->parameters = new ParameterBag([]);

        if (isset($query[3])) {
            $c_query = count($query);

            for ($i = 3; $i < $c_query; ++$i) {
                // Position
                if (preg_match('/^(page_(\d+))$/', $query[$i])) {
                    $this->parameters->add('page', (int)substr($query[$i], 5));
                } elseif (preg_match('/^(id_(\d+))$/', $query[$i])) { // ID eines Datensatzes
                    $this->parameters->add('id', (int)substr($query[$i], 3));
                } elseif (preg_match('/^(([a-z0-9-]+)_(.+))$/', $query[$i])) { // Additional URI parameters
                    $param = explode('_', $query[$i], 2);
                    $this->parameters->add($param[0], $param[1]);
                }
            }
        }

        $this->parameters->set('cat', (int)$this->getPost()->get('cat', $this->parameters->get('cat')));
        $this->parameters->set('action', $this->getPost()->get('action', $this->parameters->get('action')));
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    protected function completeQuery($query)
    {
        if (!isset($query[0])) {
            $this->query = $this->module . '/';
        }
        if (!isset($query[1])) {
            $this->query .= $this->controller . '/';
        }
        if (!isset($query[2])) {
            $this->query .= $this->controllerAction . '/';
        }

        return $query;
    }

    /**
     * Annehmen, dass ein URI Alias mit zusätzlichen Parametern übergeben wurde
     *
     * @return string[]
     */
    protected function checkUriAliasForAdditionalParameters()
    {
        $params = '';
        $probableQuery = $this->query;
        if (preg_match('/^([a-z]{1}[a-z\d\-]*\/)([a-z\d\-]+\/)+(([a-z\d\-]+)_(.+)\/)+$/', $this->query)) {
            $query = preg_split('=/=', $this->query, -1, PREG_SPLIT_NO_EMPTY);
            if (isset($query[1]) === false) {
                $query[1] = 'index';
            }
            if (isset($query[2]) === false) {
                $query[2] = 'index';
            }

            // Keine entsprechende Module-Action gefunden -> muss Alias sein
            if ($this->controllerActionExists->controllerActionExists($this->area . '/' . $query[0] . '/' . $query[1] . '/' . $query[2]) === false) {
                $length = 0;
                foreach ($query as $row) {
                    if (strpos($row, '_') !== false) {
                        break;
                    }

                    $length += strlen($row) + 1;
                }
                $params = substr($this->query, $length);
                $probableQuery = substr($this->query, 0, $length);
            }
        }

        return [$params, $probableQuery];
    }
}
