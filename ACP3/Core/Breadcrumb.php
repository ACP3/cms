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
    protected $stepsFromDb = array();
    /**
     * Enthält alle Schritte der Brotkrümelspur,
     * welche von den Modulen festgelegt werden
     *
     * @var array
     * @access private
     */
    protected $stepsFromModules = array();

    /**
     * @var array
     */
    protected $title = array('separator' => '-', 'prefix' => '', 'postfix' => '');

    /**
     * Enthält die gecachete Brotkrümelspur
     *
     * @var array
     */
    protected $breadcrumbCache = array();

    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;

    /**
     * @var \ACP3\Core\URI
     */
    protected $uri;

    /**
     * @var \ACP3\Core\View
     */
    protected $view;

    public function __construct(\Doctrine\DBAL\Connection $db, Lang $lang, URI $uri, View $view)
    {
        $this->lang = $lang;
        $this->uri = $uri;
        $this->view = $view;

        // Frontendbereich
        if (defined('IN_ADM') === false) {
            $uri = $this->uri;
            $in = array($uri->query, $uri->getUriWithoutPages(), $uri->mod . '/' . $uri->file . '/', $uri->mod);
            $items = $db->executeQuery('SELECT p.title, p.uri, p.left_id, p.right_id FROM ' . DB_PRE . 'menu_items AS c, ' . DB_PRE . 'menu_items AS p WHERE c.left_id BETWEEN p.left_id AND p.right_id AND c.uri IN(?) GROUP BY p.uri ORDER BY p.left_id ASC', array($in), array(\Doctrine\DBAL\Connection::PARAM_STR_ARRAY))->fetchAll();
            $c_items = count($items);

            // Dynamische Seite (ACP3 intern)
            for ($i = 0; $i < $c_items; ++$i) {
                $this->appendFromDB($items[$i]['title'], $uri->route($items[$i]['uri']));
            }
        }
    }

    /**
     *
     * @param string $value
     */
    public function setTitleSeparator($value)
    {
        $this->title['separator'] = $value;

        return $this;
    }

    /**
     *
     * @param string $value
     */
    public function setTitlePrefix($value)
    {
        $this->title['prefix'] = $value;

        return $this;
    }

    /**
     *
     * @param string $value
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
     * @return \bBreadcrumb
     */
    protected function appendFromDB($title, $path = 0)
    {
        $this->stepsFromDb[] = array(
            'title' => $title,
            'uri' => $path
        );

        return $this;
    }

    /**
     * Zuweisung einer neuen Stufe zur Brotkrümelspur
     *
     * @param string $title
     *    Bezeichnung der jeweiligen Stufe der Brotkrume
     * @param string $path
     *    Die zum $title zugehörige ACP3-interne URI
     * @return \bBreadcrumb
     */
    public function append($title, $path = 0)
    {
        $this->stepsFromModules[] = array(
            'title' => $title,
            'uri' => $path
        );

        return $this;
    }

    /**
     * Fügt Brotkrumen an den Anfang an
     *
     * @param string $title
     *    Bezeichnung der jeweiligen Stufe der Brotkrume
     * @param string $path
     *    Die zum $title zugehörige ACP3-interne URI
     * @return \bBreadcrumb
     */
    protected function prepend($title, $path)
    {
        $step = array(
            'title' => $title,
            'uri' => $path,
        );
        array_unshift($this->stepsFromModules, $step);
        return $this;
    }

    /**
     * Ersetzt die aktuell letzte Brotkrume mit neuen Werten
     *
     * @param string $title
     *    Bezeichnung der jeweiligen Stufe der Brotkrume
     * @param string $path
     *    Die zum $title zugehörige ACP3-interne URI
     * @return \bBreadcrumb
     */
    public function replaceAnchestor($title, $path = 0, $db_steps = false)
    {
        if ($db_steps === true) {
            $index = count($this->stepsFromDb) - (!empty($this->stepsFromDb) ? 1 : 0);
            $this->stepsFromDb[$index]['title'] = $title;
            $this->stepsFromDb[$index]['uri'] = $path;
        } else {
            $index = count($this->stepsFromModules) - (!empty($this->stepsFromModules) ? 1 : 0);
            $this->stepsFromModules[$index]['title'] = $title;
            $this->stepsFromModules[$index]['uri'] = $path;
        }

        return $this;
    }

    /**
     * Gibt je nach Modus entweder die Brotkrümelspur oder den Seitentitel aus
     *
     * @param integer $mode
     *    1 = Brotkrümelspur ausgeben
     *    2 = Nur Seitentitel ausgeben
     *  3 = Seitentitel mit eventuellen Prefixes und Postfixes ausgeben
     * @return string
     */
    public function output($mode = 1)
    {
        $module = $this->uri->mod;
        $file = $this->uri->file;

        if (empty($this->breadcrumbCache)) {
            // Brotkrümelspur für das Admin-Panel
            if (defined('IN_ADM') === true) {
                if ($module !== 'acp')
                    $this->setTitlePostfix($this->lang->t('system', 'acp'));

                // Wenn noch keine Brotkrümelspur gesetzt ist, dies nun tun
                if (empty($this->stepsFromModules)) {
                    $this->append($this->lang->t('system', 'acp'), $this->uri->route('acp'));
                    if ($module !== 'errors') {
                        if ($module !== 'acp') {
                            $this->append($this->lang->t($module, $module), $this->uri->route('acp/' . $module));
                            if ($file !== 'acp_list')
                                $this->append($this->lang->t($module, $file), $this->uri->route('acp/' . $module . '/' . $file));
                        }
                    } else {
                        $this->append($this->lang->t($module, $file), $this->uri->route('acp/' . $module . '/' . $file));
                    }
                    // Falls bereits Stufen gesetzt wurden, Links für das Admin-Panel und
                    // die Modulverwaltung in umgedrehter Reihenfolge voranstellen
                } else {
                    if ($module !== 'acp')
                        $this->prepend($this->lang->t($module, $module), $this->uri->route('acp/' . $module));
                    $this->prepend($this->lang->t('system', 'acp'), $this->uri->route('acp'));
                }
                $this->breadcrumbCache = $this->stepsFromModules;
                // Brotkrümelspur für das Frontend
            } else {
                if (empty($this->stepsFromDb) && empty($this->stepsFromModules)) {
                    $this->append($file === 'list' ? $this->lang->t($module, $module) : $this->lang->t($module, $file), $this->uri->route($module . '/' . $file));
                    $this->breadcrumbCache = $this->stepsFromModules;
                } elseif (!empty($this->stepsFromDb) && empty($this->stepsFromModules)) {
                    $this->breadcrumbCache = $this->stepsFromDb;
                } elseif (!empty($this->stepsFromModules) && empty($this->stepsFromDb)) {
                    $this->breadcrumbCache = $this->stepsFromModules;
                } else {
                    $this->breadcrumbCache = $this->stepsFromDb;

                    if ($this->breadcrumbCache[count($this->breadcrumbCache) - 1]['uri'] === $this->stepsFromModules[0]['uri']) {
                        $c_steps_mods = count($this->stepsFromModules);
                        for ($i = 1; $i < $c_steps_mods; ++$i) {
                            $this->breadcrumbCache[] = $this->stepsFromModules[$i];
                        }
                    }
                }
            }

            // Letzte Brotkrume markieren
            $this->breadcrumbCache[count($this->breadcrumbCache) - 1]['last'] = true;
        }

        // Brotkrümelspur ausgeben
        if ($mode === 1) {
            $this->view->assign('breadcrumb', $this->breadcrumbCache);
            return $this->view->fetchTemplate('system/breadcrumb.tpl');
            // Nur Titel ausgeben
        } else {
            // Letzter Eintrag der Brotkrümelspur ist der Seitentitel
            $title = $this->breadcrumbCache[count($this->breadcrumbCache) - 1]['title'];
            if ($mode === 3) {
                $separator = ' ' . $this->title['separator'] . ' ';
                if (!empty($this->title['prefix']))
                    $title = $this->title['prefix'] . $separator . $title;
                if (!empty($this->title['postfix']))
                    $title .= $separator . $this->title['postfix'];
                $title .= ' | ' . CONFIG_SEO_TITLE;
            }
            return $title;
        }
    }
}