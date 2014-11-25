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
     * @var Core\DB
     */
    protected $db;
    /**
     * @var Core\Validator\Rules\Router
     */
    protected $routerValidator;

    /**
     * @param Core\DB $db
     * @param Core\Validator\Rules\Router $routerValidator
     */
    public function __construct(
        Core\DB $db,
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
     *
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
                    return $this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . 'seo WHERE alias = ? AND uri != ?', [$alias, $path]) > 0;
                } elseif ($this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . 'seo WHERE alias = ?', [$alias]) > 0) {
                    return true;
                }
            }
        }
        return false;
    }

} 