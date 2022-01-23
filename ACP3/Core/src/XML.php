<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Application\Bootstrap;

/**
 * Parses XML files.
 */
class XML
{
    /**
     * Cache for already parsed XML files.
     *
     * @var array<string, mixed>
     */
    protected array $info = [];

    /**
     * Parses the given XML file and returns it as an array.
     *
     * @return array<string, mixed>
     */
    public function parseXmlFile(string $path, string $xpath): array
    {
        if (!empty($this->info[$path][$xpath])) {
            return $this->info[$path][$xpath];
        }

        if (is_file($path) === false) {
            return [];
        }

        /** @var \SimpleXMLElement $xml */
        $xml = simplexml_load_string(file_get_contents($path));
        $data = $xml->xpath($xpath);

        if (empty($data)) {
            return [];
        }

        foreach ($data as $row) {
            foreach ($row as $key => $value) {
                /** @var \SimpleXMLElement $value */
                if ($value->attributes()) {
                    $this->parseAttributes($value->attributes(), $path, $xpath, $key);
                } elseif (isset($this->info[$path][$xpath][$key]) && \is_array($this->info[$path][$xpath][$key])) {
                    $this->info[$path][$xpath][$key][] = (string) $value;
                } elseif (isset($this->info[$path][$xpath][$key])) {
                    $tmp = $this->info[$path][$xpath][$key];
                    $this->info[$path][$xpath][$key] = [];
                    $this->info[$path][$xpath][$key][] = $tmp;
                    $this->info[$path][$xpath][$key][] = (string) $value;
                } else {
                    $this->info[$path][$xpath][$key] = (string) $value;
                }
            }
        }

        return $this->info[$path][$xpath];
    }

    protected function parseAttributes(\SimpleXMLElement $attributes, string $path, string $xpath, string $key): void
    {
        foreach ($attributes as $attrKey => $attrValue) {
            if ($key === 'version' && $attrKey === 'core' && (string) $attrValue === 'true') {
                $this->info[$path][$xpath]['version'] = Bootstrap::VERSION;
            } else {
                $this->info[$path][$xpath][(string) $key][$attrKey] = (string) $attrValue;
            }
        }
    }
}
