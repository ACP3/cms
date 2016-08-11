<?php
namespace ACP3\Core\Helpers;

use ACP3\Core;
use Cocur\Slugify\Slugify;

/**
 * Class StringFormatter
 * @package ACP3\Core\Helpers
 */
class StringFormatter
{
    /**
     * @var Slugify
     */
    protected $slugify;

    /**
     * StringFormatter constructor.
     * @param Slugify $slugify
     */
    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    /**
     * @param string $var
     *
     * @return string
     */
    public function makeStringUrlSafe($var)
    {
        $var = html_entity_decode(strip_tags($var));

        return $this->slugify->slugify($var, '-');
    }

    /**
     * Converts new lines to HTML paragraphs and/or line breaks
     *
     * @param string $data
     * @param boolean $useLineBreaks
     *
     * @return string
     */
    public function nl2p($data, $useLineBreaks = false)
    {
        $data = trim($data);
        $pattern = "/([\n]{1,})/i";
        $replace = "</p>\n<p>";

        if ($useLineBreaks === true) {
            $pattern = [
                "/([\n]{2,})/i", // multiple new lines
                "/([^>])\n([^<])/i" // get the remaining new lines
            ];
            $replace = [
                "</p>\n<p>",
                '${1}<br>${2}'
            ];
        }

        return '<p>' . preg_replace($pattern, $replace, $data) . '</p>';
    }

    /**
     * Shortens a string to the given length
     *
     * @param string $data
     * @param integer $chars
     * @param integer $offset
     * @param string $append
     *
     * @return string
     */
    public function shortenEntry($data, $chars = 300, $offset = 50, $append = '')
    {
        if ($chars <= $offset) {
            throw new \InvalidArgumentException('The offset should not be bigger then the to be displayed characters.');
        }

        $shortened = utf8_decode(html_entity_decode(strip_tags($data), ENT_QUOTES, 'UTF-8'));
        if (strlen($shortened) > $chars && strlen($shortened) - $chars >= $offset) {
            return utf8_encode(substr($shortened, 0, $chars - $offset)) . $append;
        }

        return $data;
    }
}
