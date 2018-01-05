<?php
namespace ACP3\Core\Validation\ValidationRules;

use ACP3\Core\I18n\AvailableLanguagePacks;

class LanguagePackExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var AvailableLanguagePacks
     */
    private $availableLanguagePacks;

    /**
     * LanguagePackExistsValidationRule constructor.
     *
     * @param AvailableLanguagePacks $availableLanguagePacks
     * @param \ACP3\Core\I18n\TranslatorInterface $translator
     */
    public function __construct(AvailableLanguagePacks $availableLanguagePacks)
    {
        $this->availableLanguagePacks = $availableLanguagePacks;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->availableLanguagePacks->languagePackExists($data);
    }
}
