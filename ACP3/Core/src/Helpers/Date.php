<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core\I18n\Translator;

class Date
{
    public function __construct(private readonly Translator $translator, private readonly Forms $formsHelper)
    {
    }

    /**
     * Liefert ein Array mit allen Zeitzonen dieser Welt aus.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getTimeZones(?string $currentValue = ''): array
    {
        /** @var array<string, string[]> $timeZones */
        $timeZones = [
            'Africa' => \DateTimeZone::listIdentifiers(\DateTimeZone::AFRICA),
            'America' => \DateTimeZone::listIdentifiers(\DateTimeZone::AMERICA),
            'Antarctica' => \DateTimeZone::listIdentifiers(\DateTimeZone::ANTARCTICA),
            'Arctic' => \DateTimeZone::listIdentifiers(\DateTimeZone::ARCTIC),
            'Asia' => \DateTimeZone::listIdentifiers(\DateTimeZone::ASIA),
            'Atlantic' => \DateTimeZone::listIdentifiers(\DateTimeZone::ATLANTIC),
            'Australia' => \DateTimeZone::listIdentifiers(\DateTimeZone::AUSTRALIA),
            'Europe' => \DateTimeZone::listIdentifiers(\DateTimeZone::EUROPE),
            'Indian' => \DateTimeZone::listIdentifiers(\DateTimeZone::INDIAN),
            'Pacific' => \DateTimeZone::listIdentifiers(\DateTimeZone::PACIFIC),
            'UTC' => \DateTimeZone::listIdentifiers(\DateTimeZone::UTC),
        ];

        $result = [];
        foreach ($timeZones as $key => $timeZoneIdentifiers) {
            $result[$key] = [];
            foreach ($timeZoneIdentifiers as $timeZoneIdentifier) {
                $result[$key][$timeZoneIdentifier]['selected'] = $this->formsHelper->selectEntry(
                    'date_time_zone',
                    $timeZoneIdentifier,
                    $currentValue
                );
            }
        }

        return $result;
    }

    /**
     * Gibt ein Array mit den möglichen Datumsformaten zurück,
     * um diese als Dropdown-Menü darstellen zu können.
     *
     * @return array<string, mixed>[]
     */
    public function dateFormatDropdown(string $currentDateFormat = ''): array
    {
        $dateFormats = [
            'short' => $this->translator->t('system', 'date_format_short'),
            'long' => $this->translator->t('system', 'date_format_long'),
        ];

        return $this->formsHelper->choicesGenerator('dateformat', $dateFormats, $currentDateFormat);
    }
}
