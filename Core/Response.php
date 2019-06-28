<?php
namespace Core;

class Response {

    protected static $instance = null;
    public static function Instance() {
        if(is_null(static::$instance)) {
            static::$instance = new static(config('frame.response'));
        }
        return static::$instance;
    }

    protected $format   = 'json';
    protected $template = [];

    public function __construct($c)
    {
        $this->template = $c['template'];
    }

    public function __call($name, $arguments)
    {
        if(isset($this->template[$name])) {
            $action = $this->template[$name];
            $data   = $action($arguments);
        } else {
            $data = ["ApiFrame: response template non existent."];
        }
        $this->result($data);
    }

    public function result($data) {
        if(!is_array($data)) {
            $data = [$data];
        }
        if(config('frame.debug')) {
            dd($data);
        }
        $respString = json_encode($data, true);
        header("Content-Type:application/json; charset=utf-8");
        exit($respString);
    }
}