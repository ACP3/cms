<?php
namespace ACP3\Core\Validation\ValidationRules;

use ACP3\Core\I18n\TranslatorInterface;

/**
 * Class LanguagePackExistsValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
class LanguagePackExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Core\I18n\TranslatorInterface
     */
    protected $translator;

    /**
     * LanguagePackExistsValidationRule constructor.
     *
     * @param \ACP3\Core\I18n\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
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
