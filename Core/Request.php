<?php
namespace Core;

class Request {

    protected static $instance = null;
    public static function Instance() {
        if(is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    protected $param       = [];
    protected $param_get   = [];
    protected $param_put   = [];
    protected $param_post  = [];

    protected $param_files   = [];
    protected $param_header  = [];
    protected $param_cookie  = [];
    protected $param_session = [];
    protected $param_server  = [];

    public function __construct() {
        $this->param_get  = $_GET;
        $this->param_post = $_POST;
    }

    public function load($key, $data = null) {
        if(is_null($data) AND is_array($key)) {
            foreach($key as $k => $v) {
                $k = 'param_' . $k;
                $this->$k = array_merge($this->$k, $v);
            }
        } else if(is_array($key)) {
            $this->$key = array_merge($this->$key, $data);
        }
        return $this;
    }

    public function input($key) {
        $keyArray = [];$valueArray = [];
        if(is_array($key)) {
            $keyArray   = $key;
        } else {
            $keyArray[] = $key;
        }
        foreach($keyArray as $k) {
            if(isset($this->param[$k])) {
                $valueArray[$k] = $this->param[$k];
            } else if(isset($this->param_get[$k])) {
                $valueArray[$k] = $this->param_get[$k];
            } else if(isset($this->param_put[$k])) {
                $valueArray[$k] = $this->param_put[$k];
            } else if(isset($this->param_post[$k])) {
                $valueArray[$k] = $this->param_post[$k];
            } else if(isset($this->param_cookie[$k])) {
                $valueArray[$k] = $this->param_cookie[$k];
            } else if(isset($this->param_session[$k])) {
                $valueArray[$k] = $this->param_session[$k];
            } else if(isset($this->param_files[$k])) {
                $valueArray[$k] = $this->param_files[$k];
            } else if(isset($this->param_header[$k])) {
                $valueArray[$k] = $this->param_header[$k];
            } else if(isset($this->param_server[$k])) {
                $valueArray[$k] = $this->param_server[$k];
            } else {
                $valueArray[$k] = null;
            }
        }
        if(is_array($key)) {
            return array_values($valueArray);
        } else {
            return $valueArray[$key];
        }
    }

    public function param($key, $data = null, $type = null) {
        if(is_null($type)) {
            if(is_null($data)) {
                if(is_array($key)) {
                    $pArray = [];
                    foreach($key as $vk) {
                        if(isset($this->param[$vk])) {
                            $pArray[$vk] = $this->param[$vk];
                        } else {
                            $pArray[$vk] = null;
                        }
                    }
                    return array_values($pArray);
                }else if(isset($this->param[$key])) {
                    return $this->param[$key];
                }
                return null;
            } else {
                $this->param[$key] = $data;
                return $this;
            }
        } else {
            $param = 'param_' . $type;
            if(is_null($data)) {
                if(is_array($key)) {
                    $pArray = [];
                    foreach($key as $vk) {
                        if(isset($this->$param[$vk])) {
                            $pArray[$vk] = $this->$param[$vk];
                        } else {
                            $pArray[$vk] = null;
                        }
                    }
                    return array_values($pArray);
                }else if(isset($this->$param[$key])) {
                    return $this->$param[$key];
                }
                return null;
            } else {
                $this->$param[$key] = $data;
                return $this;
            }
        }
    }

    public function get($key, $data = null) {
        return $this->param($key, $data, 'get');
    }

    public function put($key, $data = null) {
        return $this->param($key, $data, 'put');
    }

    public function post($key, $data = null) {
        return $this->param($key, $data, 'post');
    }

    public function route($key, $data = null) {
        return $this->param($key, $data, 'route');
    }


    // 获取客户IP
    public function ip() {
        static $ip = '';
        $ip = $_SERVER['REMOTE_ADDR'];
        if(isset($_SERVER['HTTP_CDN_SRC_IP'])) {
            $ip = $_SERVER['HTTP_CDN_SRC_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        if (preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $ip)) {
            return $ip;
        } else {
            return '127.0.0.1';
        }
    }

    // 获取当前域名
    public function host() {
        return $_SERVER['SERVER_NAME'];
    }

    // 当前端口
    public function port() {
        return $_SERVER["SERVER_PORT"];
    }

    // 请求URL
    public function request_uri() {
        return $_SERVER["REQUEST_URI"];
    }

    // 获取当前协议
    public function scheme() {
        return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https' : 'http';
    }

    // 获取当前URL
    public function url($uri = null) {
        $url = config('app.url');
        if(is_null($url)) {
            // dd($_SERVER['SERVER_NAME']);
            $url  = $this->scheme() . "://" . $this->host();
            $port = $this->port();
            if(!in_array($port, [443, 80, 21, 23])) {
                $url .= ":" . $port;
            }
            if(is_null($uri)) {
                $url .= $this->request_uri();
            } else {
                $url .= $uri;
            }
        }
        return $url;
    }
    
}