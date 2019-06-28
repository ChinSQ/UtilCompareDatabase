<?php
namespace Core;

class App {

    protected static $instance = null;
    public static function Instance() {
        if(is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function run() {
        $p1 = request('method'); $p1 = empty($p1)?'index':$p1;
        $p2 = explode('.', $p1);
        if(count($p2) == 1) {
            $pClass  = $p2[0];
            $pMethod = 'index';
        } else {
            $pClass  = $p2[0];
            $pMethod = $p2[1];
        }
        $pClass = "\App\Action\\".ucfirst($pClass);
        if(class_exists($pClass)) {
            $pObj   = new $pClass();
            if(is_callable([$pObj, $pMethod])) {
                $pObj->$pMethod(Request::Instance());
            } else {
                return response()->result("ApiFrame: method '$p1' non existent.");
            }
        } else {
            return response()->result("ApiFrame: method '$p1' non existent.");
        }
    }
}