<?php

namespace U89Man\Database;

use PDO;
use PDOException;
use PDOStatement;

class DB
{
    /**
     * @var int
     */
    protected static $queryCounter = 0;

    /**
     * @var array
     */
    protected static $sqlList = [];

    /**
     * @var PDO
     */
    protected $pdo;


    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param string $sql
     * @param array|null $params
     *
     * @return PDOStatement|null
     */
    public function query($sql, array $params = null)
    {
        try {
            $sql = trim($sql, ' ;');

            $stmt = $this->pdo->prepare($sql);

            self::$sqlList[] = ['sql' => $sql, 'params' => $params];

            if ($stmt->execute($params)) {
                self::$queryCounter++;

                return $stmt;
            }
        }
        catch (PDOException $e) {
            $this->report($e);
        }

        return null;
    }

    /**
     * @return int
     */
    public static function getQueryCounter()
    {
        return self::$queryCounter;
    }

    /**
     * @return array
     */
    public static function getSqlList()
    {
        return self::$sqlList;
    }

    /**
     * @param PDOException $e
     *
     * @return void
     */
    public function report(PDOException $e)
    {
        throw $e;
    }

    /**
     * @param string $table
     * @param array $data
     *
     * @return int
     */
    public function insert($table, array $data = array())
    {
        $params = [];
        foreach ($data as $key => $value) {
            $params[':'.$key] = $value;
        }

        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_keys($params));

        $sql = 'INSERT INTO '.$table.' ('.$columns.') VALUES ('.$values.')';

        if ($this->query($sql, $params)) {
            return (int) $this->pdo->lastInsertId();
        }

        return -1;
    }

    /**
     * @param string $table
     * @param string[]|string $columns
     * @param string|null $where
     * @param string|null $order
     * @param array|null $params
     *
     * @return array
     */
    public function select($table, $columns = '*', $where = null, $order = null, $params = null)
    {
        $columns = is_string($columns) ? $columns : implode(', ', $columns);

        $sql = 'SELECT '.$columns.' FROM '.$table;

        if ($where)
            $sql .= ' WHERE '.$where;

        if ($order)
            $sql .= ' ORDER BY '.$order;

        if ($stmt = $this->query($sql, $params)) {
            return $stmt->fetchAll();
        }

        return [];
    }

    /**
     * @param string $table
     * @param array $data
     * @param string|null $where
     * @param array|null $params
     *
     * @return int
     */
    public function update($table, array $data, $where = null, array $params = null)
    {
        $func = function ($k, $v) {
            return $k.' = '.(is_string($v) && ($v != '?' || substr($v, 0, 1) != ':') ? '"'.$v.'"' : $v);
        };

        $setData = array_map($func, array_keys($data), array_values($data));

        $sql = 'UPDATE '.$table.' SET '.implode(', ', $setData);

        if ($where)
            $sql .= ' WHERE '.$where;

        if ($stmt = $this->query($sql, $params)) {
            return $stmt->rowCount();
        }

        return -1;
    }

    /**
     * @param string $table
     * @param string|null $where
     * @param array|null $params
     *
     * @return int
     */
    public function delete($table, $where = null, array $params = null)
    {
        $sql = 'DELETE FROM '.$table;

        if ($where)
            $sql .= ' WHERE '.$where;

        if ($stmt = $this->query($sql, $params)) {
            return $stmt->rowCount();
        }

        return -1;
    }
}

