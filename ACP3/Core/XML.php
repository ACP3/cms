<?php
namespace ACP3\Core;

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
            $xml = simplexml_load_file($path);
            $data = $xml->xpath($xpath);

            if (!empty($data)) {
                foreach ($data as $row) {
                    foreach ($row as $key => $value) {
                        if ($value->attributes()) {
                            foreach ($value->attributes() as $attrKey => $attrValue) {
                                if ($key === 'version' && $attrKey === 'core' && (string)$attrValue === 'true') {
                                    $this->info[$path][$xpath]['version'] = Application::VERSION;
                                } else {
                                    $this->info[$path][$xpath][(string)$key][(string)$attrKey] = (string)$attrValue;
                                }
                            }
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
}
