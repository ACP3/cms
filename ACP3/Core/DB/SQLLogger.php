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
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->start = microtime(true);
        $this->queries[$_SERVER['REQUEST_URI']][++$this->currentQuery] = ['sql' => $sql, 'params' => $params, 'types' => $types, 'executionMS' => 0];
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        $this->queries[$_SERVER['REQUEST_URI']][$this->currentQuery]['executionMS'] = microtime(true) - $this->start;
    }

    public function __destruct()
    {
        $totalTime = 0;
        foreach ($this->queries[$_SERVER['REQUEST_URI']] as $query) {
            $totalTime += $query['executionMS'];
        }

        $this->queries[$_SERVER['REQUEST_URI']]['queryCount'] = count($this->queries[$_SERVER['REQUEST_URI']]);
        $this->queries[$_SERVER['REQUEST_URI']]['totalTime'] = $totalTime;

        Logger::debug($this->logFilename, $this->queries);
    }
}