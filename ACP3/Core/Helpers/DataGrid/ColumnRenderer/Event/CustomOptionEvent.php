<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer\Event;

use ACP3\Core\Helpers\DataGrid\ColumnRenderer\OptionsColumnRenderer\OptionRenderer;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CustomOptionEvent
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer\Event
 */
class CustomOptionEvent extends Event
{
    /**
     * @var \ACP3\Core\Helpers\DataGrid\ColumnRenderer\OptionsColumnRenderer\OptionRenderer
     */
    protected $optionRenderer;
    /**
     * @var array
     */
    private $dbResultRow;
    /**
     * @var string
     */
    protected $identifier;

    /**
     * CustomOptionEvent constructor.
     *
     * @param \ACP3\Core\Helpers\DataGrid\ColumnRenderer\OptionsColumnRenderer\OptionRenderer $optionRenderer
     * @param array                                                                           $dbResultRow
     * @param string                                                                          $identifier
     */
    public function __construct(OptionRenderer $optionRenderer, array $dbResultRow, $identifier)
    {
        $this->optionRenderer = $optionRenderer;
        $this->identifier = $identifier;
        $this->dbResultRow = $dbResultRow;
    }

    /**
     * @return \ACP3\Core\Helpers\DataGrid\ColumnRenderer\OptionsColumnRenderer\OptionRenderer
     */
    public function getOptionRenderer()
    {
        return $this->optionRenderer;
    }

    /**
     * @return array
     */
    public function getDbResultRow()
    {
        return $this->dbResultRow;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}