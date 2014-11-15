<?php
namespace ACP3\Core\Router;

use ACP3\Core;
use ACP3\Modules\System;

/**
 * Class Aliases
 * @package ACP3\Core\Router
 */
class Aliases
{
    /**
     * @var array
     */
    protected $aliases = [];

    /**
     * @var \ACP3\Core\Cache
     */
    protected $cache;
    /**
     * @var Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Modules\System\Model
     */
    protected $systemModel;

    /**
     * Zerlegt u.a. die übergebenen Parameter in der URI in ihre Bestandteile
     */
    function __construct(\Doctrine\DBAL\Connection $db)
    {
        $this->systemModel = new System\Model($db);
        $this->cache = new Core\Cache('uri');
        $this->aliases = $this->getCache();
    }

    /**
     * Gibt den Cache der URI-Aliase zurück
     *
     * @return array
     */
    public function getCache()
    {
        if ($this->cache->contains('aliases') === false) {
            $this->setCache();
        }

        return $this->cache->fetch('aliases');
    }

    /**
     * Setzt den Cache für die URI-Aliase
     *
     * @return boolean
     */
    protected function setCache()
    {
        $aliases = $this->systemModel->getAllUriAliases();
        $c_aliases = count($aliases);
        $data = array();

        for ($i = 0; $i < $c_aliases; ++$i) {
            $data[$aliases[$i]['uri']] = array(
                'alias' => $aliases[$i]['alias'],
            );
        }

        return $this->cache->save('aliases', $data);
    }

    /**
     * Gibt einen URI-Alias zurück
     *
     * @param string $path
     * @param bool   $emptyIsNoResult
     *
     * @return string
     */
    public function getUriAlias($path, $emptyIsNoResult = false)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return !empty($this->aliases[$path]['alias']) ? $this->aliases[$path]['alias'] : ($emptyIsNoResult === true ? '' : $path);
    }

    /**
     * Löscht einen URI-Alias
     *
     * @param string $path
     *
     * @return boolean
     */
    public function deleteUriAlias($path)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        $bool = $this->systemModel->delete($path, 'uri', System\Model::TABLE_NAME_SEO);
        return $bool !== false && $this->setCache() !== false;
    }

    /**
     * Trägt einen URI-Alias in die Datenbank ein bzw. aktualisiert den Eintrag
     *
     * @param string $path
     * @param string $alias
     * @param string $keywords
     * @param string $description
     * @param int    $robots
     *
     * @return boolean
     */
    public function insertUriAlias($path, $alias, $keywords = '', $description = '', $robots = 0)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';
        $keywords = Core\Functions::strEncode($keywords);
        $description = Core\Functions::strEncode($description);
        $values = array(
            'alias' => $alias,
            'keywords' => $keywords,
            'description' => $description,
            'robots' => (int)$robots
        );

        // Update an existing result
        if ($this->systemModel->uriAliasExists($path) === true) {
            $bool = $this->systemModel->update($values, array('uri' => $path), System\Model::TABLE_NAME_SEO);
        } else {
            $values['uri'] = $path;
            $bool = $this->systemModel->insert($values, System\Model::TABLE_NAME_SEO);
        }

        return $bool !== false && $this->setCache() !== false;
    }

    /**
     * Überprüft, ob ein URI-Alias existiert
     *
     * @param string $path
     *
     * @return boolean
     */
    public function uriAliasExists($path)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return isset($this->aliases[$path]) === true && !empty($this->aliases[$path]['alias']);
    }
} 