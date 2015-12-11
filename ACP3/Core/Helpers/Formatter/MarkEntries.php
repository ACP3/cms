<?php
namespace ACP3\Core\Helpers\Formatter;

use ACP3\Core\I18n\Translator;

/**
 * Class MarkEntries
 * @package ACP3\Core\Helpers\Formatter
 */
class MarkEntries
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $lang;

    /**
     * MarkEntries constructor.
     *
     * @param \ACP3\Core\I18n\Translator $lang
     */
    public function __construct(Translator $lang)
    {
        $this->lang = $lang;
    }

    /**
     * @param string $name
     * @param string $markAllId
     *
     * @return string
     */
    public function execute($name, $markAllId = '')
    {
        $markAllId = !empty($markAllId) ? $markAllId : 'mark-all';
        $deleteOptions = json_encode(
            [
                'checkBoxName' => $name,
                'language' => [
                    'confirmationTextSingle' => $this->lang->t('system', 'confirm_delete_single'),
                    'confirmationTextMultiple' => $this->lang->t('system', 'confirm_delete_multiple'),
                    'noEntriesSelectedText' => $this->lang->t('system', 'no_entries_selected')
                ]
            ]
        );

        return 'data-mark-all-id="' . $markAllId . '" data-checkbox-name="' . $name . '" data-delete-options=\'' . $deleteOptions . '\'';
    }
}