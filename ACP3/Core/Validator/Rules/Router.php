<?php
namespace ACP3\Core\Validator\Rules;
use ACP3\Core\Validator\ValidationRules\InternalUriValidationRule;
use ACP3\Core\Validator\ValidationRules\UriSafeValidationRule;

/**
 * Class Router
 * @package ACP3\Core\Validator\Rules
 *
 * @deprecated
 */
class Router
{
    /**
     * @var \ACP3\Core\Validator\ValidationRules\UriSafeValidationRule
     */
    protected $uriSafeValidationRule;
    /**
     * @var \ACP3\Core\Validator\ValidationRules\InternalUriValidationRule
     */
    protected $internalUriValidationRule;

    /**
     * Router constructor.
     *
     * @param \ACP3\Core\Validator\ValidationRules\UriSafeValidationRule     $uriSafeValidationRule
     * @param \ACP3\Core\Validator\ValidationRules\InternalUriValidationRule $internalUriValidationRule
     */
    public function __construct(
        UriSafeValidationRule $uriSafeValidationRule,
        InternalUriValidationRule $internalUriValidationRule
    )
    {
        $this->uriSafeValidationRule = $uriSafeValidationRule;
        $this->internalUriValidationRule = $internalUriValidationRule;
    }

    /**
     * Überprüft, ob der eingegebene URI-Alias sicher ist, d.h. es dürfen nur
     * die Kleinbuchstaben von a-z, Zahlen, der Bindestrich und das Slash eingegeben werden
     *
     * @param string $var
     *
     * @return boolean
     *
     * @deprecated
     */
    public function isUriSafe($var)
    {
        return $this->uriSafeValidationRule->isValid($var);
    }

    /**
     * Überprüft, ob die übergebene URI dem Format des ACP3 entspricht
     *
     * @param mixed $var
     *
     * @return boolean
     *
     * @deprecated
     */
    public function isInternalURI($var)
    {
        return $this->internalUriValidationRule->isValid($var);
    }
}
