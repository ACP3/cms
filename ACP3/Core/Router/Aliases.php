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
    protected $uriCache;
    /**
     * @var Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Modules\System\Model
     */
    protected $systemModel;

    /**
     * @param Core\Cache $uriCache
     * @param System\Model $systemModel
     */
    function __construct(
        Core\Cache $uriCache,
        System\Model $systemModel
    )
    {
        $this->uriCache = $uriCache;
        $this->systemModel = $systemModel;
        $this->aliases = $this->getCache();
    }

    /**
     * Gibt den Cache der URI-Aliase zurück
     *
     * @return array
     */
    public function getCache()
    {
        if ($this->uriCache->contains('aliases') === false) {
            $this->setCache();
        }

        return $this->uriCache->fetch('aliases');
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
        $data = [];

        for ($i = 0; $i < $c_aliases; ++$i) {
            $data[$aliases[$i]['uri']] = [
                'alias' => $aliases[$i]['alias'],
            ];
        }

        return $this->uriCache->save('aliases', $data);
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
        $values = [
            'alias' => $alias,
            'keywords' => $keywords,
            'description' => $description,
            'robots' => (int)$robots
        ];

        // Update an existing result
        if ($this->systemModel->uriAliasExists($path) === true) {
            $bool = $this->systemModel->update($values, ['uri' => $path], System\Model::TABLE_NAME_SEO);
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