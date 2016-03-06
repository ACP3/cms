<?php
namespace ACP3\Core\Helpers;

use ACP3\Core;

/**
 * Class StringFormatter
 * @package ACP3\Core\Helpers
 */
class StringFormatter
{
    /**
     * Macht einen String URL sicher
     *
     * @param string $var
     *
     * @return string
     */
    public static function makeStringUrlSafe($var)
    {
        $var = strip_tags($var);
        if (!preg_match('/&([a-z]+);/', $var)) {
            $var = htmlentities($var, ENT_QUOTES, 'UTF-8');
        }

        $search = [
            '/&([a-z]{1})uml;/',
            '/&szlig;/',
            '/&([a-z0-9]+);/',
            '/(\s+)/',
            '/-{2,}/',
            '/[^a-z0-9-]/',
        ];
        $replace = [
            '${1}e',
            'ss',
            '',
            '-',
            '-',
            '',
        ];

        return preg_replace($search, $replace, strtolower($var));
    }

    /**
     * Konvertiert Zeilenumbrüche zu neuen Absätzen
     *
     * @param string  $data
     * @param boolean $lineBreaks
     *
     * @return string
     */
    public function nl2p($data, $lineBreaks = false)
    {
        $data = trim($data);
        if ($lineBreaks === true) {
            return '<p>' . preg_replace(["/([\n]{2,})/i", "/([^>])\n([^<])/i"], ["</p>\n<p>", '<br>'], $data) . '</p>';
        }

        return '<p>' . preg_replace("/([\n]{1,})/i", "</p>\n<p>", $data) . '</p>';
    }

    /**
     * Kürzt einen String, welcher im UTF-8-Charset vorliegt
     * auf eine bestimmte Länge
     *
     * @param string  $data
     * @param integer $chars
     * @param integer $offset
     * @param string  $append
     *
     * @return string
     */
    public function shortenEntry($data, $chars = 300, $offset = 50, $append = '')
    {
        if ($chars <= $offset) {
            $offset = 0;
        }

        $shortened = utf8_decode(html_entity_decode(strip_tags($data), ENT_QUOTES, 'UTF-8'));
        if (strlen($shortened) > $chars && strlen($shortened) - $chars >= $offset) {
            return utf8_encode(substr($shortened, 0, $chars - $offset)) . $append;
        }

        return $data;
    }
}
