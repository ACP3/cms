<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use Cocur\Slugify\Slugify;

class StringFormatter
{
    public function __construct(private readonly Slugify $slugify)
    {
    }

    public function makeStringUrlSafe(string $var): string
    {
        $var = html_entity_decode(strip_tags($var));

        return $this->slugify->slugify($var, '-');
    }

    /**
     * Converts new lines to HTML paragraphs and/or line breaks.
     */
    public function nl2p(string $data, bool $useLineBreaks = false): string
    {
        $data = trim($data);
        $pattern = "/([\n]{1,})/i";
        $replace = "</p>\n<p>";

        if ($useLineBreaks === true) {
            $pattern = [
                "/([\n]{2,})/i", // multiple new lines
                "/([^>])\n([^<])/i", // get the remaining new lines
            ];
            $replace = [
                "</p>\n<p>",
                '${1}<br>${2}',
            ];
        }

        return '<p>' . preg_replace($pattern, $replace, $data) . '</p>';
    }

    /**
     * Shortens a string to the given length.
     */
    public function shortenEntry(string $data, int $chars = 300, int $offset = 50, string $append = ''): string
    {
        if ($chars <= $offset) {
            throw new \InvalidArgumentException('The offset should not be bigger than the to be displayed characters.');
        }

        $shortened = strip_tags($data);
        if (mb_strlen($shortened) > $chars && mb_strlen($shortened) - $chars >= $offset) {
            return mb_substr($shortened, 0, $chars - $offset) . $append;
        }

        return $data;
    }
}
