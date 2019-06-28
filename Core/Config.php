<?php
namespace Core;

class Config {

    protected $data = [];

    protected static $instance = null;
    public static function Instance() {
        if(is_null(static::$instance)) {
            static::$instance = new static(__require(ROOT_PATH . 'convention' . PHP_EXT));
        }
        return static::$instance;
    }

    public function __construct($data = []) {
        $this->data = $data;
    }

    public function load($data) {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function setMerge($key, $data) {
        $this->data[$key] = array_merge($this->data[$key], $data);
        return $this;
    }

    // 无限级设置
    public function set($keyString, $value = null) {
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

    // 无限级获取
    public function get($keyString, $default = null) {
        $keys = explode('.', $keyString);
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

    // 删除配置
    public function del($keyString) {
        $keys    = explode('.', $keyString);
        $lastKey = $keys[count($keys) - 1];
        unset($keys[count($keys) - 1]);
        $then = &$this->data;
        if(count($keys) > 0) {
            foreach($keys as $k) {
                if(isset($then[$k])) {
                    $then = &$then[$k];
                } else {
                    return $this;
                }
            }
        }
        unset($then[$lastKey]);
        return $this;
    }

    // 保存配置到
    public function storeTo($keys = [], $path = '') {
        $newData = null;
        foreach($keys as $k) {
            $newData[$k] = $this->get($k);
        }
        $fileText = jsonToPhpFormat($newData);
        return file_put_contents($path, $fileText);
    }

    // 保存核心配置
    public function storeConvention() {
        return $this->storeTo(['database', 'app', 'frame'], ROOT_PATH . 'convention' . PHP_EXT);
    }
}