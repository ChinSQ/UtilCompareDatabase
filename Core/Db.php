<?php
namespace Core;

class Db {

    protected $conf  = [];
    protected $conn  = null;
    protected $param_table = null;
    protected $param_where = [];
    protected $param_order = [];
    protected $param_group = [];
    protected $param_data  = [];
    protected $param_field = [];
    protected $isTransaction = false;

    public function __construct($c = [])
    {
        $this->conf = $c;
        $dsn = "{$c['drive']}:host={$c['host']};dbname={$c['database']}";
        $this->conn = new \PDO($dsn, $c['username'], $c['password']);
        $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->query("set character set '{$c['charset']}'");
    }

    // 启动事务
    public function beginTransaction() {
        $this->conn->beginTransaction();
        $this->isTransaction = true;
    }

    // 提交事务
    public function commitTransaction() {
        $this->conn->commit();
        $this->isTransaction = false;
    }

    // 回滚事务
    public function rollBackTransaction() {
        $this->conn->rollBack();
        $this->isTransaction = false;
    }

    // 是否开启事务
    public function isTransaction() {
        return $this->isTransaction;
    }

    // 删除数据
    public function delete($table, $sql_where, $row = true) {
        $sql = "DELETE FROM `{$table}` WHERE {$sql_where}";
//        dd($sql);
        return $this->exec($sql, $row);
    }

    // 更新数据
    public function update($table, $data, $sql_where, $row = true) {
        $sql_data = "";
        foreach($data as $k => $v) {
            $sql_data .= "`{$k}`='{$v}',";
        }
        $sql_data = rtrim($sql_data, ',');
        $sql = "UPDATE `{$table}` SET {$sql_data} WHERE {$sql_where}";
        return $this->exec($sql, $row);
    }

    // 插入数据
    public function insert($table, $data, $row = true) {
        $sql_data = ""; $sql_key = "";
        foreach($data as $k => $v) {
            $sql_key  .= "`{$k}`,";
            $sql_data .= "'{$v}',";
        }
        $sql_key  = rtrim($sql_key, ',');
        $sql_data = rtrim($sql_data, ',');
        $sql = "INSERT INTO `{$table}` ({$sql_key}) VALUES({$sql_data})";
        if($this->exec($sql, $row)) {
            return $this->lastInsertId();
        }
        return false;
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

    // 错误信息
    public function error() {
        return $this->conn->errorInfo();
    }

    // 执行一个SQL
    public function exec($sql, $row = false) {
        $is = $this->conn->exec($sql);
        if($is === false OR ($row AND $is == 0)) {
            if($this->isTransaction()) {
                throw new \Exception("[SQL] {$sql}");
            }
            return false;
        }
        return $is;
    }

    // 执行一个查询
    public function query($sql) {
        return $this->conn->query($sql);
    }

    // 获取一个字段
    public function findColumn($sql) {
        $sql  = $sql . " LIMIT 1";
        $stmt = $this->query($sql); $data = null;
        if($stmt !== false) {
            $data = $stmt->fetch(\PDO::FETCH_BOTH);
            $data = $data[0];
            $stmt->closeCursor();
        }
        return $data;
    }

    // 获取一行
    public function find($sql) {
        $sql  = $sql . " LIMIT 1";
        $stmt = $this->query($sql); $data = null;
        if($stmt !== false) {
            $data = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        }
        return $data;
    }

    public function exist($sql) {
        return $this->find($sql) !== false;
    }

    // 获取多行
    public function findAll($sql) {
        $stmt = $this->query($sql); $data = [];
        if($stmt !== false) {
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        }
        return $data;
    }

    public function tables() {
        $database   = $this->conf['database'];
        $sql        = "select table_name from information_schema.tables where table_schema='{$database}'";
        $tableArray = $this->findAll($sql);
        $tableArray = array_column($tableArray, 'table_name');
        return $tableArray;
    }

    public function columns($table) {
        $database   = $this->conf['database'];
        $sql = "select COLUMN_NAME from information_schema.COLUMNS where table_name = '{$table}' and table_schema = '{$database}'";
        $columnArray = $this->findAll($sql);
        $columnArray = array_column($columnArray, 'COLUMN_NAME');
        return $columnArray;
    }

    public function __destruct()
    {
        if(!is_null($this->conn)) {
            $this->conn = null;
        }
    }

}