<?php
namespace ACP3\Core\Modules;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Filesystem;

/**
 * Class Vendors
 * @package ACP3\Core\Modules
 */
class Vendors
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var array
     */
    protected $vendors = [];

    /**
     * Vendors constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
    }

    /**
     * @return array
     */
    public function getVendors()
    {
        if ($this->vendors === []) {
            $this->vendors = array_merge(
                ['ACP3'],
                Filesystem::scandir($this->appPath->getModulesDir(), ['ACP3', 'Custom']),
                ['Custom']
            );
        }

        return $this->vendors;
    }
}