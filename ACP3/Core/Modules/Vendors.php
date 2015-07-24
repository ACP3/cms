<?php
namespace ACP3\Core\Modules;

/**
 * Class Vendors
 * @package ACP3\Core\Modules
 */
class Vendors
{
    /**
     * @var array
     */
    protected $vendors = [];

    /**
     * @return array
     */
    public function getVendors()
    {
        if ($this->vendors === []) {
            $this->vendors = array_merge(
                ['ACP3'],
                array_diff(scandir(MODULES_DIR), ['.', '..', 'ACP3', 'Custom']),
                ['Custom']
            );
        }

        return $this->vendors;
    }
}