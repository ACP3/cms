<?php
namespace ACP3\Core\DB;

use ACP3\Core\Logger;

/**
 * Class SQLLogger
 * @package ACP3\Core\DB
 */
class SQLLogger implements \Doctrine\DBAL\Logging\SQLLogger
{
    /**
     * @var \ACP3\Core\Logger
     */
    protected $logger;

    /**
     * Executed SQL queries.
     *
     * @var array
     */
    public $queries = [];

    /**
     * @var float|null
     */
    public $start = null;

    /**
     * @var integer
     */
    public $currentQuery = 0;

    /**
     * @var string
     */
    private $logFilename = 'db-queries';
    /**
     * @var string
     */
    private $requestPath = '';

    /**
     * SQLLogger constructor.
     *
     * @param \ACP3\Core\Logger $logger
     */
    public function __construct(Logger $logger)
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
        $this->queries[$this->requestPath][++$this->currentQuery] = ['sql' => $sql, 'params' => $params, 'types' => $types, 'executionMS' => 0];
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

            $this->logger->debug($this->logFilename, $this->queries);
        }
    }
}
