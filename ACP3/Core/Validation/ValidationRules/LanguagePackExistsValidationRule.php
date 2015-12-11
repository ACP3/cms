<?php
namespace ACP3\Core\Validation\ValidationRules;

use ACP3\Core\I18n\Translator;

/**
 * Class LanguagePackExistsValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
class LanguagePackExistsValidationRule extends AbstractValidationRule
{
    const NAME = 'language_pack_exists';

    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $lang;

    /**
     * LanguagePackExistsValidationRule constructor.
     *
     * @param \ACP3\Core\I18n\Translator $lang
     */
    public function __construct(Translator $lang)
    {
        $this->lang = $lang;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->lang->languagePackExists($data);
    }
}