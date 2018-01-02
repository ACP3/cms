<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Database;

use Psr\Log\LoggerInterface;

/**
 * Class SQLLogger
 * @package ACP3\Core\Database
 */
class SQLLogger implements \Doctrine\DBAL\Logging\SQLLogger
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * Executed SQL queries.
     *
     * @var array
     */
    private $queries = [];
    /**
     * @var float|null
     */
    private $start = null;
    /**
     * @var integer
     */
    private $currentQuery = 0;
    /**
     * @var string
     */
    private $requestPath = '';

    /**
     * SQLLogger constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->requestPath = $_SERVER['REQUEST_URI'];
    }

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->start = microtime(true);
        $this->queries[$this->requestPath][++$this->currentQuery] = [
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
            'executionMS' => 0
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        $this->queries[$this->requestPath][$this->currentQuery]['executionMS'] = microtime(true) - $this->start;
    }

    public function __destruct()
    {
        if (isset($this->queries[$this->requestPath])) {
            $totalTime = 0;
            foreach ($this->queries[$this->requestPath] as $query) {
                $totalTime += $query['executionMS'];
            }

            $this->queries[$this->requestPath]['queryCount'] = count($this->queries[$this->requestPath]);
            $this->queries[$this->requestPath]['totalTime'] = $totalTime;

            $this->logger->debug('Executed queries for: ' . $this->requestPath, $this->queries);
        }
    }
}
