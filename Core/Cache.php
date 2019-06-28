<?php
namespace Core;

/**
 * Cache class
 */
class Cache {

    protected static $instance = null;
    public static function Instance() {
        if(is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    protected $data = []; // 每个key第一个参数作为缓存文件名 主索引

    public function set($keyString, $value) {
        $keys = explode('.', $keyString);
        $then = &$this->data;
        foreach($keys as $k) {
            if(isset($then[$k])) {
                $then = &$then[$k];
            } else {
                $then[$k] = [];
                $then = &$then[$k];
            }
        }
        $then = $value;
        return $this;
    }

    public function get($keyString, $default = null) {
        $keys = explode('.', $keyString);
        if(!isset($this->data[$keys[0]])) {
            if($this->loadCache($keys[0]) === false) {
                return null;
            }
        }
        $then = &$this->data;
        foreach($keys as $k) {
            if(isset($then[$k])) {
                $then = &$then[$k];
            } else {
                return $default;
            }
        }
        return $then;
    }

    // 加载缓存文件
    public function loadCache($key) {
        $fileName = md5($key); $filePath = CACHE_PATH . $fileName;
        if(!file_exists($filePath)) {
            return false;
        }
        $data = file_get_contents($filePath);
        $type = mb_substr($data, 0, 3);
        $data = mb_substr($data, 3);
        if($type == 'NUL') {
            $this->data[$key] = null;
        } else if($type == 'BOL') {
            $this->data[$key] = boolval($data);
        } else if($type == 'STR') {
            $this->data[$key] = $data;
        } else if($type == 'NUM') {
            $this->data[$key] = $data;
        } else if($type == 'OBJ') {
            $this->data[$key] = json_decode($data);
        } else if($type == 'ARR') {
            $this->data[$key] = json_decode($data);
        }
        return true;
    }

    // 保存缓存文件
    public function storeCache($key) {
        $fileName = md5($key);
        $data = $this->data[$key];
        if(is_null($data)) {
            $data = "NUL";
        } else if(is_bool($data)) {
            $data = "BOL" . $data;
        } else if(is_string($data)) {
            $data = "STR" . $data;
        } else if(is_numeric($data)) {
            $data = "NUM" . $data;
        } else if(is_object($data)) {
            $data = "OBJ" . json_encode($data);
        } else if(is_array($data)) {
            $data = "ARR" . json_encode($data);
        } else {
            response()->result("ApiFrame: Cache Type not store.");
        }
        return file_put_contents($filePath, $data);
    }

    // 保存
    public function storeAll() {
        foreach($this->data as $key => $v) {
            $this->storeCache($key);
        }
    }
}