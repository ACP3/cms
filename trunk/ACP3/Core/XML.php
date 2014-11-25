<?php
namespace ACP3\Core;

use ACP3\Application;

/**
 * Parst XML Dateien, z.B. die diversen info.xml bzw. module.xml Dateien
 * @package ACP3\Core
 */
class XML
{
    /**
     * Cache für bereits ausgelesene XML-Dateien
     *
     * @var array
     */
    protected $info = [];

    /**
     * Parst die angeforderte XML Datei
     *
     * @param string $path
     * @param string $xpath
     *
     * @return mixed
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
