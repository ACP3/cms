<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Helper;

use ACP3\Core\XML;

trait AvailableDesignsTrait
{
    protected function getAvailableDesigns(): array
    {
        $designs = [];
        foreach ($this->getDesignPaths() as $file) {
            $designInfo = $this->getXml()->parseXmlFile($file, '/design');
            if (!empty($designInfo)) {
                $directory = $this->getDesignDirectory($file);
                $designs[] = \array_merge(
                    $designInfo,
                    [
                        'selected' => $this->selectEntry($directory),
                        'dir' => $directory,
                    ]
                );
            }
        }

        return $designs;
    }

    /**
     * @return XML
     */
    abstract protected function getXml();

    private function getDesignPaths(): array
    {
        return \glob(ACP3_ROOT_DIR . '/designs/*/info.xml');
    }

    private function getDesignDirectory(string $file): string
    {
        $pathLength = \strlen(ACP3_ROOT_DIR . '/designs/');
        $lastDS = \strrpos($file, '/');

        return \substr($file, $pathLength, $lastDS - $pathLength);
    }

    /**
     * @param string $directory
     *
     * @return string
     */
    abstract protected function selectEntry($directory);
}
