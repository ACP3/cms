<?php
namespace ACP3\Core\Validator\Rules\Router;

use ACP3\Core;

/**
 * Class Aliases
 * @package ACP3\Core\Validator\Rules\Router
 */
class Aliases
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Core\Validator\Rules\Router
     */
    protected $routerValidator;

    public function __construct(
        \Doctrine\DBAL\Connection $db,
        Core\Validator\Rules\Router $routerValidator
    )
    {
        $this->db = $db;
        $this->routerValidator = $routerValidator;
    }

    /**
     * Überprüft, ob ein URI-Alias bereits existiert
     *
     * @param string $alias
     * @param string $path
     * @return boolean
     */
    public function uriAliasExists($alias, $path = '')
    {
        if ($this->routerValidator->isUriSafe($alias)) {
            if (is_dir(MODULES_DIR . $alias) === true) {
                return true;
            } else {
                $path .= !preg_match('=/$=', $path) ? '/' : '';
                if ($path !== '/' && $this->routerValidator->isInternalURI($path) === true) {
                    return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'seo WHERE alias = ? AND uri != ?', array($alias, $path)) > 0;
                } elseif ($this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'seo WHERE alias = ?', array($alias)) > 0) {
                    return true;
                }
            }
        }
        return true;
    }

} 