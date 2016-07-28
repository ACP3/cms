<?php
namespace ACP3\Core\Validation\ValidationRules;

use ACP3\Core\I18n\Translator;

/**
 * Class LanguagePackExistsValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
class LanguagePackExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;

    /**
     * LanguagePackExistsValidationRule constructor.
     *
     * @param \ACP3\Core\I18n\Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->translator->languagePackExists($data);
    }
}
