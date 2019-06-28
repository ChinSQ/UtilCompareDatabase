<?php
require  "boot.php";


const SHOW_A   = 1; // 只看A的差异
const SHOW_B   = 2; // 只看B的差异
const SHOW_ALL = 3; // 查看所有差异

// 基础配置
$dbNameA  = 'w7';        // 数据库A
$dbNameB  = 'yun';       // 数据库B
$prefixA  = 'ims_ybsc_'; // 过滤前缀
$prefixB  = 'ims_ybsc_'; // 过滤前缀
$showMode = SHOW_ALL;      // 显示模式


dump(">>> 对比两个数据库 <b>A</b> {$dbNameA}.{$prefixA}* 和 <b>B</b> {$dbNameB}.{$prefixB}*");

// 获取数据库
$dbA = db($dbNameA);
$dbB = db($dbNameB);

// 获取所有表
$tableArrayA = $dbA->tables();
$tableArrayB = $dbB->tables();

// 过滤表前缀 并去除前缀
$tableArrayA = filterTableArray($tableArrayA, $prefixA);
$tableArrayB = filterTableArray($tableArrayB, $prefixB);

// 差异表查询
$diffTableArrayA = []; // A 缺少 B 的表
$diffTableArrayB = []; // B 缺少 A 的表
foreach ($tableArrayB as $table) {
    if(!in_array($table, $tableArrayA)) {
        $diffTableArrayA[] = $table;
    }
}
foreach ($tableArrayA as $table) {
    if(!in_array($table, $tableArrayB)) {
        $diffTableArrayB[] = $table;
    }
}
dump(">>> 查询表差异集");
if(count($diffTableArrayA) > 0 && ($showMode == SHOW_ALL OR $showMode == SHOW_A)) {
    dump("A 缺少 B 的表", '#ffcb8a');
    dump($diffTableArrayA, '#ffcb8a');
}
if(count($diffTableArrayB) > 0 && ($showMode == SHOW_ALL OR $showMode == SHOW_B)) {
    dump("B 缺少 A 的表", '#ffcb8a');
    dump($diffTableArrayB, '#ffcb8a');
}

// 查询相同表字段差异
$intersectTableArray = array_intersect($tableArrayA, $tableArrayB); // 获取表交集

dump(">>> 查询相同表字段差异集");
foreach ($intersectTableArray as $currentTable) {
    $columnArrayA = $dbA->columns($prefixA . $currentTable); // 表A的所有字段
    $columnArrayB = $dbB->columns($prefixB . $currentTable); // 表B的所有字段
    $diffColumnArrayA = []; // 表A 缺少 表B 字段
    $diffColumnArrayB = []; // 表B 缺少 表A 字段

    foreach ($columnArrayB as $column) {
        if(!in_array($column, $columnArrayA)) {
            $diffColumnArrayA[] = $column;
        }
    }
    foreach ($columnArrayA as $column) {
        if(!in_array($column, $columnArrayB)) {
            $diffColumnArrayB[] = $column;
        }
    }

    if(count($diffColumnArrayA) > 0 OR count($diffColumnArrayB) > 0) {
        dump(">>>>>> 正在对比表 {$currentTable}", '#87d0fb');
    }

    if(count($diffColumnArrayA) > 0  && ($showMode == SHOW_ALL OR $showMode == SHOW_A)) {
        dump("A 缺少 B 的字段", '#ffcb8a');
        dump($diffColumnArrayA, '#ffcb8a');
    }
    if(count($diffColumnArrayB) > 0  && ($showMode == SHOW_ALL OR $showMode == SHOW_B)) {
        dump("B 缺少 A 的字段", '#ffcb8a');
        dump($diffColumnArrayB, '#ffcb8a');
    }
}





/**
 * 表前缀过滤
 * @param array $tableArray
 * @param string $prefix
 * @return array
 */
function filterTableArray($tableArray, $prefix) {
    foreach ($tableArray as $k => $str) {
        if(substr($str, 0, strlen($prefix)) != $prefix) {
            unset($tableArray[$k]);
        } else {
            $tableArray[$k] = substr($str, strlen($prefix));
        }
    }
    sort($tableArray);
    return $tableArray;
}
