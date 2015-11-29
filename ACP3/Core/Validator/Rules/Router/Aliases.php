<?php
namespace ACP3\Core\Validator\Rules\Router;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

/**
 * Class Aliases
 * @package ACP3\Core\Validator\Rules\Router
 *
 * @deprecated
 */
class Aliases
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Validator\ValidationRules\UriAliasValidationRule
     */
    protected $uriAliasValidationRule;

    /**
     * Aliases constructor.
     *
     * @param \ACP3\Modules\ACP3\Seo\Validator\ValidationRules\UriAliasValidationRule $uriAliasValidationRule
     */
    public function __construct(
        Seo\Validator\ValidationRules\UriAliasValidationRule $uriAliasValidationRule
    )
    {
        $this->uriAliasValidationRule = $uriAliasValidationRule;
    }

    /**
     * Überprüft, ob ein URI-Alias bereits existiert
     *
     * @param string $alias
     * @param string $path
     *
     * @return boolean
     *
     * @deprecated
     */
    public function uriAliasExists($alias, $path = '')
    {
        return $this->uriAliasValidationRule->isValid($alias, '', ['path' => $path]);
    }
}
