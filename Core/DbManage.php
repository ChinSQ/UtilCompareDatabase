<?php
namespace Core;
/**
 * DbManage class
 */
class DbManage {

    protected static $dbConnon = [];
    public static function connon($key) {
        if(!isset(static::$dbConnon[$key])) {
            $c = Config::Instance()->get("database.{$key}");
            if(is_null($c)) {
                return null;
            }
            static::$dbConnon[$key] = new Db($c);
        }
        return static::$dbConnon[$key];
    }
}