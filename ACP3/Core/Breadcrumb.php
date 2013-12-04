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
    private $steps_db = array();
    /**
     * Enthält alle Schritte der Brotkrümelspur,
     * welche von den Modulen festgelegt werden
     *
     * @var array
     * @access private
     */
    private $steps_mods = array();

    /**
     * @var array
     */
    private $title = array('separator' => '-', 'prefix' => '', 'postfix' => '');

    /**
     * Enthält die gecachete Brotkrümelspur
     *
     * @var array
     */
    private $breadcrumb_cache = array();

    /**
     * @var \ACP3\Core\Lang
     */
    private $lang;

    /**
     * @var \ACP3\Core\URI
     */
    private $uri;

    /**
     * @var \ACP3\Core\View
     */
    private $view;

    public function __construct(\Doctrine\DBAL\Connection $db, \ACP3\Core\Lang $lang, \ACP3\Core\URI $uri, \ACP3\Core\View $view)
    {
        $this->lang = $lang;
        $this->uri = $uri;
        $this->view = $view;

        // Frontendbereich
        if (defined('IN_ADM') === false) {
            $uri = $this->uri;
            $in = array($uri->query, $uri->getCleanQuery(), $uri->mod . '/' . $uri->file . '/', $uri->mod);
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
    private function appendFromDB($title, $path = 0)
    {
        $this->steps_db[] = array(
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
        $this->steps_mods[] = array(
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
    private function prepend($title, $path)
    {
        $step = array(
            'title' => $title,
            'uri' => $path,
        );
        array_unshift($this->steps_mods, $step);
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
            $index = count($this->steps_db) - (!empty($this->steps_db) ? 1 : 0);
            $this->steps_db[$index]['title'] = $title;
            $this->steps_db[$index]['uri'] = $path;
        } else {
            $index = count($this->steps_mods) - (!empty($this->steps_mods) ? 1 : 0);
            $this->steps_mods[$index]['title'] = $title;
            $this->steps_mods[$index]['uri'] = $path;
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

        if (empty($this->breadcrumb_cache)) {
            // Brotkrümelspur für das Admin-Panel
            if (defined('IN_ADM') === true) {
                if ($module !== 'acp')
                    $this->setTitlePostfix($this->lang->t('system', 'acp'));

                // Wenn noch keine Brotkrümelspur gesetzt ist, dies nun tun
                if (empty($this->steps_mods)) {
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
                $this->breadcrumb_cache = $this->steps_mods;
                // Brotkrümelspur für das Frontend
            } else {
                if (empty($this->steps_db) && empty($this->steps_mods)) {
                    $this->append($file === 'list' ? $this->lang->t($module, $module) : $this->lang->t($module, $file), $this->uri->route($module . '/' . $file));
                    $this->breadcrumb_cache = $this->steps_mods;
                } elseif (!empty($this->steps_db) && empty($this->steps_mods)) {
                    $this->breadcrumb_cache = $this->steps_db;
                } elseif (!empty($this->steps_mods) && empty($this->steps_db)) {
                    $this->breadcrumb_cache = $this->steps_mods;
                } else {
                    $this->breadcrumb_cache = $this->steps_db;

                    if ($this->breadcrumb_cache[count($this->breadcrumb_cache) - 1]['uri'] === $this->steps_mods[0]['uri']) {
                        $c_steps_mods = count($this->steps_mods);
                        for ($i = 1; $i < $c_steps_mods; ++$i) {
                            $this->breadcrumb_cache[] = $this->steps_mods[$i];
                        }
                    }
                }
            }

            // Letzte Brotkrume markieren
            $this->breadcrumb_cache[count($this->breadcrumb_cache) - 1]['last'] = true;
        }

        // Brotkrümelspur ausgeben
        if ($mode === 1) {
            $this->view->assign('breadcrumb', $this->breadcrumb_cache);
            return $this->view->fetchTemplate('system/breadcrumb.tpl');
            // Nur Titel ausgeben
        } else {
            // Letzter Eintrag der Brotkrümelspur ist der Seitentitel
            $title = $this->breadcrumb_cache[count($this->breadcrumb_cache) - 1]['title'];
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