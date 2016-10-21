<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\DataProcessor;


use ACP3\Core\Model\DataProcessor\ColumnType\ColumnTypeStrategyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ColumnTypeStrategyFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * ColumnTypeStrategyFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $columnType
     * @return ColumnTypeStrategyInterface
     */
    public function getStrategy($columnType)
    {
        $serviceId = 'core.model.column_type.' . $columnType . '_column_type_strategy';
        /** @var ColumnTypeStrategyInterface $service */
        $service = $this->container->get($serviceId);
        return $service;
    }
}
