<?php
use Core\Cache;

function response() {
    return \Core\Response::Instance();
}

function dd($obj, $background = '#dcdcdc', $color = "#666") {
    dump($obj, $background, $color);die;
}

function dump($obj, $background = '#dcdcdc', $color = "#666") {
    $preL = "<pre style='background-color: {$background};color: {$color};padding: 10px 10px;'>"; $preR = "</pre>";
    if(is_null($obj) OR is_numeric($obj) OR is_bool($obj)) {
        echo $preL;
        var_dump($obj);
        echo $preR;
    } else if(is_string($obj)) {
        echo $preL;
        echo $obj;
        echo $preR;
    } else {
        echo $preL;
        print_r($obj);
        echo $preR;
    }
}

function __include($file) {
    return include $file;
}

function __require($file) {
    return require $file;
}

function app() {
    return \Core\App::Instance();
}

function findPathStringFile($path, $ext = true) {
    $file = substr($path, strlen(dirname($path)) + 1);
    if($ext) {
        return $file;
    }
    return str_replace(strrchr($file, "."),"",$file);
}

// 获取请求数据
function request($key = null, $data = null, $type = null) {
    $requset = \Core\Request::Instance();
    if(is_null($key) AND is_null($data) AND is_null($type)) {
        return $requset;
    } else if(is_null($data) AND is_null($type)) {
        return $requset->input($key);
    } else {
        return $requset->param($key, $data, $type);
    }
}

// 获取配置
function config($key = null, $value = null) {
    if(is_null($key)) {
        return \Core\Config::Instance();
    } else if(is_null($value)) {
        return \Core\Config::Instance()->get($key);
    } else {
        \Core\Config::Instance()->set($key, $value);
    }
}

//循环删除目录和文件函数 
function delDirAndFile($dirName) {
    if(is_dir($dirName)) {
        $res = findDirAndFiles($dirName);
    }
    if($res !== null) {
        foreach($res['file'] as $file) {
            unlink($file) AND out("成功删除文件：$file");
        }
        foreach($res['dir'] as $dir) {
            rmdir($dir) AND out("成功删除目录：$dir");
        }
        rmdir($dirName) AND out("成功删除目录：$dirName");
    
        out("文件删除完毕.");
    }
}

// 获取路径下 所有文件夹 和 文件
function findDirAndFiles($path) {
    $list = scandir($path); $fileList = []; $dirList = []; $nodeDirList = [];
    foreach($list as $row) {
        if(!in_array($row, ['.', '..'])) {
            $p = $path . $row;
            if(is_dir($p)) {
                $dirList[]     = $p . DS;
                $nodeDirList[] = $p . DS;
            } else {
                $fileList[] = $p;
            }
        }
    }
    // 获取目录下文件文件夹
    foreach($nodeDirList as $dir) {
        $res      = findDirAndFiles($dir);
        $fileList = array_merge($fileList, $res['file']);
        $dirList  = array_merge($dirList, $res['dir']);
    }
    return ['file'=>$fileList, 'dir'=>$dirList];
}

/**
 * Json数据格式化为PHP文件
 *
 * @param Mixed $data
 * @return String
 */
function jsonToPhpFormat($data) {
    $text = "<?php return ";
    $textData = jsonFormat($data, "\t");
    $textData = str_replace('{', '[', $textData);
    $textData = str_replace('}', ']', $textData);
    $textData = str_replace(':', '=>', $textData);
    $text = $text . $textData . ";";
    return $text;
}

/** 
 * Json数据格式化
 * @param  Mixed  $data   数据
 * @param  String $indent 缩进字符，默认4个空格
 * @return JSON
*/
function jsonFormat($data, $indent=null){
    // 对数组中每个元素递归进行urlencode操作，保护中文字符
    array_walk_recursive($data, 'jsonFormatProtect');
    // json encode
    $data = json_encode($data);
    // 将urlencode的内容进行urldecode
    $data = urldecode($data);
    // 缩进处理
    $ret = '';
    $pos = 0;
    $length = strlen($data);
    $indent = isset($indent)? $indent : '    ';
    $newline = "\n";
    $prevchar = '';
    $outofquotes = true;
    for($i=0; $i<=$length; $i++){
        $char = substr($data, $i, 1);
        if($char=='"' && $prevchar!='\\'){
            $outofquotes = !$outofquotes;
        }elseif(($char=='}' || $char==']') && $outofquotes){
            $ret .= $newline;
            $pos --;
            for($j=0; $j<$pos; $j++){
                $ret .= $indent;
            }
        }
        $ret .= $char;
        if(($char==',' || $char=='{' || $char=='[') && $outofquotes){
            $ret .= $newline;
            if($char=='{' || $char=='['){
                $pos ++;
            }
            for($j=0; $j<$pos; $j++){
                $ret .= $indent;
            }
        }
        $prevchar = $char;
    }
    return $ret;
}

/** 
 * 将数组元素进行urlencode
 * @param String &$val
 */
function jsonFormatProtect(&$val){
    if($val!==true && $val!==false && $val!==null){
        $val = urlencode($val);
    }
}

/**
 * 获取数据库
 * @param String $key
 * @return \Core\Db|Null
 */
function db($key) {
    return \Core\DbManage::connon($key);
}

/**
 * 获取缓存
 * @param String $key
 * @return \Core\Cache|Null
 */
function cache($key = null, $value = null) {
    $cache = \Core\Cache::Instance();
    if(is_null($key) AND is_null($value)) {
        return $cache;
    } else if(is_null($value)) {
        return $cache->get($key);
    } else {
        return $cache->set($key, $value);
    }
}

/**
 * -----------------------------------------------
 * 
 *  下面是业务函数
 * 
 * 
 * 
 * 
 * 
 * ------------------------------------------------
 */

/**
 * 获取随机字符串 OR 数字
 *
 * @param int $length
 * @param boolean $numeric
 * @return void
 */
function random($length, $numeric = FALSE) {
	$seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
	if ($numeric) {
		$hash = '';
	} else {
		$hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
		$length--;
	}
	$max = strlen($seed) - 1;
	for ($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}

/**
 * 生成Hash
 *
 * @param string $string
 * @param string $salt
 * @return void
 */
function hash_mask($string, $salt = "") {
	$key = config("app.key");
	$stringA = "{$string}-{$salt}-{$key}";
	return sha1($stringA);
}

/**
 * 获取中文拼音首字母
 *
 * @param string $str
 * @return void
 */
function get_first_pinyin($str) {
	static $pinyin;
	$first_char = '';
	$str = trim($str);
	if(empty($str)) {
		return $first_char;
	}
	if (empty($pinyin)) {
		$pinyin = new \Lib\Pinyin\Pinyin();
	}
	$first_char = $pinyin->get_first_char($str);
	return $first_char;
}