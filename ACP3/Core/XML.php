<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core;

use ACP3\Core\Application\Bootstrap;

/**
 * Parses XML files
 *
 * @package ACP3\Core
 */
class XML
{
    /**
     * Cache for already parsed XML files
     *
     * @var array
     */
    protected $info = [];

    /**
     * Parses the given XML file and returns it as an array
     *
     * @param string $path
     * @param string $xpath
     *
     * @return array
     */
    public function parseXmlFile($path, $xpath)
    {
        if (!empty($this->info[$path][$xpath])) {
            return $this->info[$path][$xpath];
        } elseif (is_file($path) === true) {
            /** @var \SimpleXMLElement $xml */
            $xml = simplexml_load_file($path);
            $data = $xml->xpath($xpath);

            if (!empty($data)) {
                foreach ($data as $row) {
                    foreach ($row as $key => $value) {
                        /** @var \SimpleXMLElement $value */
                        if ($value->attributes()) {
                            $this->parseAttributes($value->attributes(), $path, $xpath, $key);
                        } elseif (isset($this->info[$path][$xpath][(string)$key]) && is_array($this->info[$path][$xpath][(string)$key])) {
                            $this->info[$path][$xpath][(string)$key][] = (string)$value;
                        } elseif (isset($this->info[$path][$xpath][(string)$key])) {
                            $tmp = $this->info[$path][$xpath][(string)$key];
                            $this->info[$path][$xpath][(string)$key] = [];
                            $this->info[$path][$xpath][(string)$key][] = $tmp;
                            $this->info[$path][$xpath][(string)$key][] = (string)$value;
                        } else {
                            $this->info[$path][$xpath][(string)$key] = (string)$value;
                        }
                    }
                }
                return $this->info[$path][$xpath];
            }
        }
        return [];
    }

    /**
     * @param \SimpleXMLElement $attributes
     * @param string            $path
     * @param string            $xpath
     * @param string            $key
     */
    protected function parseAttributes(\SimpleXMLElement $attributes, $path, $xpath, $key)
    {
        foreach ($attributes as $attrKey => $attrValue) {
            if ($key === 'version' && $attrKey === 'core' && (string)$attrValue === 'true') {
                $this->info[$path][$xpath]['version'] = Bootstrap::VERSION;
            } else {
                $this->info[$path][$xpath][(string)$key][(string)$attrKey] = (string)$attrValue;
            }
        }
    }
}
