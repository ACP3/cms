<?php
namespace ACP3\Core;

use ACP3\Modules\ACP3\Seo;

/**
 * Class Request
 * @package ACP3\Core
 */
class Request extends AbstractRequest
{
    const ADMIN_PANEL_PATTERN = '=^acp/=';

    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
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
     * Holds the trimmed query
     *
     * @var string
     */
    protected $query = '';
    /**
     * @var string
     */
    protected $originalQuery = '';
    /**
     * Holds all given query parameters
     *
     * @var array
     * @access protected
     */
    protected $params = [];
    /**
     * @var bool
     */
    protected $isHomepage;

    /**
     * @param \ACP3\Core\Modules           $modules
     * @param \ACP3\Core\Config            $config
     * @param \ACP3\Modules\ACP3\Seo\Model $seoModel
     */
    public function __construct(
        Modules $modules,
        Config $config,
        Seo\Model $seoModel
    )
    {
        $this->modules = $modules;
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
    public function getModuleAndController()
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
     * Gibt einen URI Parameter aus
     *
     * @param string $key
     *
     * @return string|integer|null
     */
    public function __get($key)
    {
        return isset($this->params[$key]) === true ? $this->params[$key] : null;
    }

    /**
     * Setzt einen neuen URI Parameter
     *
     * @param string         $key
     * @param string|integer $value
     */
    public function __set($key, $value)
    {
        // Make it impossible to overwrite already set parameters
        if (isset($this->params[$key]) === false) {
            $this->params[$key] = $value;
        }
    }

    /**
     * Überprüft, ob ein URI-Parameter existiert
     *
     * @param string $key
     *
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->params[$key]);
    }

    /**
     * @inheritdoc
     */
    public function getIsHomepage()
    {
        if ($this->isHomepage === null) {
            $this->isHomepage = ($this->query === $this->config->getSettings('system')['homepage']);
        }

        return $this->isHomepage;
    }

    /**
     * @inheritdoc
     * @see http://detectmobilebrowsers.com/download/php
     */
    public function isMobileBrowser()
    {
        $userAgent = $this->getServer()->get('HTTP_USER_AGENT', '');

        return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent) ||
        preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($userAgent, 0, 4));
    }

    /**
     * @inheritdoc
     */
    public function getParameters()
    {
        return $this->params;
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
        if (isset($query[3])) {
            $c_query = count($query);

            for ($i = 3; $i < $c_query; ++$i) {
                // Position
                if (preg_match('/^(page_(\d+))$/', $query[$i])) {
                    $this->page = (int)substr($query[$i], 5);
                } elseif (preg_match('/^(id_(\d+))$/', $query[$i])) { // ID eines Datensatzes
                    $this->id = (int)substr($query[$i], 3);
                } elseif (preg_match('/^(([a-z0-9-]+)_(.+))$/', $query[$i])) { // Additional URI parameters
                    $param = explode('_', $query[$i], 2);
                    $this->$param[0] = $param[1];
                }
            }
        }

        $this->cat = (int)$this->getPost()->get('cat', 0);
        $this->action = $this->getPost()->get('action');
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
            return $query;
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
            if ($this->modules->actionExists($this->area . '/' . $query[0] . '/' . $query[1] . '/' . $query[2]) === false) {
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
