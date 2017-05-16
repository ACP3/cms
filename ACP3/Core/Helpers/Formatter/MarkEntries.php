<?php
namespace ACP3\Core\Helpers\Formatter;

use ACP3\Core\I18n\TranslatorInterface;

/**
 * Class MarkEntries
 * @package ACP3\Core\Helpers\Formatter
 */
class MarkEntries
{
    /**
     * @var \ACP3\Core\I18n\TranslatorInterface
     */
    protected $translator;

    /**
     * MarkEntries constructor.
     *
     * @param \ACP3\Core\I18n\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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
                    'confirmationTextSingle' => $this->translator->t('system', 'confirm_delete_single'),
                    'confirmationTextMultiple' => $this->translator->t('system', 'confirm_delete_multiple'),
                    'noEntriesSelectedText' => $this->translator->t('system', 'no_entries_selected')
                ]
            ]
        );

        return 'data-mark-all-id="' . $markAllId . '" data-checkbox-name="' . $name . '" data-delete-options=\'' . $deleteOptions . '\'';
    }
}
