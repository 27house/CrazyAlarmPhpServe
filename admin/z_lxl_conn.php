<?php
session_start();
header("Content-type: text/html; charset=utf-8"); 
require_once(dirname(__FILE__).'/../360safe/360webscan.php'); 
/**
 * 递归方式的对变量中的特殊字符进行转义
 *
 * @access  public
 * @param   mix     $value
 * @author  hankcs
 *
 * @return  mix
 */
function addslashes_deep($value)
{
    if (empty($value))
    {
        return $value;
    }
     
    if(is_array($value))
    {
        foreach((array)$value as $k=>$v)
        {
            unset($value[$k]);
            $k = addslashes($k);
            if(is_array($v))
                $value[$k] = addslashes_deep($v);
            else
                $value[$k] = addslashes($v);
        }
    }
    else
    {
        $value = addslashes($value);
    }
    return $value;
}

define("EW_CONN_HOST", 'localhost', TRUE);
define("EW_CONN_PORT", 3306, TRUE);
define("EW_CONN_USER", 'root', TRUE);
define("EW_CONN_PASS", 'root', TRUE);
define("EW_CONN_DB", 'crazy', TRUE);
include_once dirname(__file__)."/amcolumn/value_indicator/mysqli/conn_utf8.php";
$db->Query("set names utf8");

global $db;
//图片服务器ip
global $image_serve_ip;
$image_serve_ip = $db->get_var("select `op_value` from `2_os_ja_option` where `op_type` = '图片服务器' limit 1");
//微信相关
$qun_name_base = $db->get_var("select `op_value` from `2_os_ja_option` where `op_type` = '微信群名称' limit 1"); //默认微信群名
$http_host = $_SERVER['HTTP_HOST'];
$sql = "select * from `weixin_xiaohao` where `domain` = '".$http_host."' or `domain` = 'www.".$http_host."'  limit 1 ";
$http_host_user = $db->get_row($sql);
global $http_host_user;
//微信客服
$wx_kefu = $http_host_user->kfwx;
//QQ客服
$qq_kefu = $http_host_user->kfqq;
if(empty($http_host_user)) dir("host 404");
define("APPID",$http_host_user->appid);
define("APPSECRET",$http_host_user->appsecret);

define("HTTP_ROOT",'http://'.$_SERVER['HTTP_HOST'].'/');
define("HTTP_ROOT_S",'https://'.$_SERVER['HTTP_HOST'].'/');
define("IMG_ROOT",'http://'.$_SERVER['HTTP_HOST'].'/upload/');
define("IMG_ROOT_S",'https://'.$_SERVER['HTTP_HOST'].'/upload/');
define("CURL_TIMEOUT",30);


//图标数组，更多ico图标请参考ace/buttons.html页面
global $ico_arr_lxl;
$ico_arr_lxl = array(
"icon-inbox",
"icon-credit-card",
"icon-flag",
"icon-gift",
"icon-circle",
"icon-group",
"icon-bolt",
"icon-home",
"icon-laptop",
"icon-external-link",
"icon-eye-open",
"icon-lightbulb",
"icon-cog",
"icon-bookmark",
);

//随机生成一个订单号($pre，订单前缀)
function order_sn($pre = ''){
	$order_sn = $pre.date('ymd').substr(time(),-5).substr(microtime(),2,5).rand(1000,9999);
	return $order_sn;
}

function insert_arr($arr,$table_name){
global $db;
if(!empty($arr)){
//先获取表的名称
$sql = "describe $table_name";
$table_list = $db->get_results($sql);
$t1 = '';
$t2 = '';
foreach($arr as $k=>$v){
foreach($table_list as $table_user){
if($table_user->Field == $k){
if($t1==""){
$t1 = '`'.$k.'`';
}else{
$t1 = $t1 . ',`'.$k.'`';
}
if($t2==""){
$t2 = '"'.addslashes($v).'"';
}else{
$t2 = $t2.',"'.addslashes($v).'"';
}
break;
}
}
}
$sql = "insert into `$table_name`($t1) values($t2)";
$re1 = $db->Query($sql);
return $re1;
}else{
return 0;
}
}

//自动编辑数据库函数（支持联合主键）
function update_arr($arr,$table_name,$where = ''){
    global $db;
    if(!empty($arr)){
        //先获取表的名称
        $sql = "describe $table_name";
        $table_list = $db->get_results($sql);
        $t1 = '';
        $t2 = '';
        $t3 = '';
        $key_zd = ''; //Key字段
        $key_value = ''; //Key值
		$sql_mul = ''; //联合主键sql语句
						
        foreach($arr as $k=>$v){
            foreach($table_list as $table_user){
                if($table_user->Field == $k){
                    if($table_user->Key == 'PRI' || $table_user->Key == 'UNI'){
                        $key_zd = $k;
                        $key_value = addslashes($v);
                    }else if($table_user->Key == 'MUL' && $sql_mul == ''){
						//联合组件
						//先找出主键
						$sql = "SHOW INDEX FROM $table_name";
						$users_zs = $db->get_results($sql);
						$key_arr = array();
						$key_mul = '';
						if(!empty($users_zs)){
							foreach($users_zs as $user){
								$Key_name = $user->Key_name;
								$Column_name = $user->Column_name;
								if($Column_name == $k){
									$key_mul = $Key_name; //找到联合组件的名称
									break;
								}
							}
							//找到联合主键所有的值，并生成sql语句
							foreach($users_zs as $user){
								$Key_name = $user->Key_name;
								$Column_name = $user->Column_name;
								if($key_mul == $Key_name){
									if(!array_key_exists($Column_name,$arr)){ //联合主键值不全
										$sql_mul = '';
										break;
									}else{
										if($arr[$Column_name]!=''){
											$sql_mul .= " and `{$Column_name}` = '".$arr[$Column_name]."'";
										}else{
											$sql_mul .= " and (`{$Column_name}` = '".$arr[$Column_name]."' or {$Column_name} is null)";
										}
										
									}
									
								}
								$sql_mul = trim($sql_mul,' and'); //去头头尾的and
							}
						}
		
					}else{
						$t1 = $k;
						$t2 = addslashes($v);
						$t3 .= " `{$t1}` = '{$t2}',";
					}
                    break;
                }
            }
        }
        $t3 = trim($t3,',');
		
        //$sql = "insert into ($t1) values($t2)";
		if(!empty($where)){
			$sql = "update `$table_name` set {$t3} where {$where} limit 1";
            @$re1 = $db->Query($sql);
            if($re1){
                return $re1;
            }else{
                return 0;
            }
			
		}else if(($key_zd != '') && ($key_value != '')){
            $sql = "update `$table_name` set {$t3} where `{$key_zd}` = '{$key_value}' limit 1";
            @$re1 = $db->Query($sql);
            if($re1){
                return $re1;
            }else{
                return 0;
            }
        }else if($sql_mul!=''){//联合主键更新sql语句
			$sql = "update `$table_name` set {$t3} where {$sql_mul} limit 1";
            @$re1 = $db->Query($sql);
            if($re1){
                return $re1;
            }else{
                return 0;
            }
			
		}else{
            return  0;
        }

    }else{
        return 0;
    }
}

function update_arr2($arr,$table_name,$where){
    global $db;
    if(!empty($arr)){
        //先获取表的名称
        $sql = "describe $table_name";
        $table_list = $db->get_results($sql);
        $t1 = '';
        $t2 = '';
        $t3 = '';
        $key_zd = ''; //Key字段
        $key_value = ''; //Key值
		$sql_mul = ''; //联合主键sql语句
						
        foreach($arr as $k=>$v){
            foreach($table_list as $table_user){
                if($table_user->Field == $k){
                    $t1 = $k;
					$t2 = addslashes($v);
					$t3 .= " `{$t1}` = '{$t2}',";
                }
            }
        }
        $t3 = trim($t3,',');
		
		$sql = "update `$table_name` set {$t3} where {$where} ";
		@$re1 = $db->Query($sql);
		if($re1){
			return $re1;
		}else{
			return 0;
		}

    }else{
        return 0;
    }
}

function select_arr($field,$table_name,$where){
	global $db;
	$sql = "select {$field} from `{$table_name}` where {$where}";
	$users = $db->get_results($sql);
	$arr = array();
	if(!empty($users)){
		$arr = json_decode(json_encode($users),true);
	}
	return $arr;
	
}

function cache_cdn($str){
	return $str;
	//return "http://cache.3cuc.com/cmmall/".$str;
}

//加密解密算法
function app_encrypt($string,$operation,$key='lxl'){
    $key=md5($key); 
    $key_length=strlen($key); 
    $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string; 
    $string_length=strlen($string); 
    $rndkey=$box=array(); 
    $result=''; 
    for($i=0;$i<=255;$i++){ 
           $rndkey[$i]=ord($key[$i%$key_length]); 
        $box[$i]=$i; 
    } 
    for($j=$i=0;$i<256;$i++){ 
        $j=($j+$box[$i]+$rndkey[$i])%256; 
        $tmp=$box[$i]; 
        $box[$i]=$box[$j]; 
        $box[$j]=$tmp; 
    } 
    for($a=$j=$i=0;$i<$string_length;$i++){ 
        $a=($a+1)%256; 
        $j=($j+$box[$a])%256; 
        $tmp=$box[$a]; 
        $box[$a]=$box[$j]; 
        $box[$j]=$tmp; 
        $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256])); 
    } 
    if($operation=='D'){ 
        if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){ 
            return substr($result,8); 
        }else{ 
            return''; 
        } 
    }else{ 
        return str_replace('=','',base64_encode($result)); 
    } 
}

function code62($x){
	$show='';
	while($x>0){
		$s=$x % 62;
		if ($s>35){
			$s=chr($s+61);
		}elseif($s>9&&$s<=35){
			$s=chr($s+55);
		}
		$show.=$s;
		$x=floor($x/62);
	}
	return $show;
}

//自动转utf-8编码
function characet($data){
  if(!empty($data)){
    $fileType = mb_detect_encoding($data , array('UTF-8','GBK','LATIN1','BIG5')) ;
    if( $fileType != 'UTF-8'){
      $data = mb_convert_encoding($data ,'utf-8' , $fileType);
    }
  }
  return $data;
}

//获取真实ip（防伪造）
function get_client_ip()
{
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
		list($re) =  explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
		return trim($re);
	}else{
		return $_SERVER['REMOTE_ADDR'];
	}
}

function curl_file_get_contents($durl,$useragent='Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36'){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $durl);
  curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 3);
    curl_setopt( $ch, CURLOPT_TIMEOUT , 3);
  curl_setopt( $ch, CURLOPT_USERAGENT , $useragent);
  curl_setopt($ch, CURLOPT_REFERER, '');//这里写一个来源地址，可以写要抓的页面的首页
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); //是否抓取跳转后的页面 
  if (substr($url, 0, 5) == 'https'); {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
  $r = curl_exec($ch);
  curl_close($ch);
  return $r;
}

function file_get_contents_cache($get_url,$bs,$catch_time = 3600){
	$file_save = dirname(__FILE__)."/cache/".$bs.'_'.md5($get_url).".html";
	if(file_exists($file_save) && filemtime($file_save) >= time() - $catch_time){
		$html = file_get_contents($file_save);
	}else{
		$html = file_get_contents($get_url);
		file_put_contents($file_save,$html);
	}
	return $html;
}

function curl_post_url($url,$params){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 要求结果为字符串且输出到屏幕上
	curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_POST, 1); // post 提交方式
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}
//非阻塞模式
function yibu_curl($url){
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt ($ch,CURLOPT_NOSIGNAL,true);//支持毫秒级别超时设置
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_TIMEOUT_MS,1); //超时1毫秒
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function GetHtmlAreaA($s, $e, $html)
{
    if($html==""||$s=="")
    {
        return "";
    }
	//$s = trimall($s);
	//$e = trimall($e);
	//$html = trimall($html);
	
    $posstart = @strpos($html,$s);
    if($posstart === FALSE)
    {
        return "";
    }
    $posend = strpos($html, $e, $posstart + strlen($posstart));
    if($posend > $posstart && $posend !== FALSE)
    {
        return substr($html, $posstart+strlen($s), $posend-$posstart-strlen($s));
    }else
    {
        return '';
    }
}

//判断是不是手机访问
function is_mobile(){
	// returns true if one of the specified mobile browsers is detected  
	// 如果监测到是指定的浏览器之一则返回true  
	$regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";  
    $regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";  
	$regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";  
	$regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";     
	$regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";  
	$regex_match.=")/i";  
	// preg_match()方法功能为匹配字符，既第二个参数所含字符是否包含第一个参数所含字符，包含则返回1既true  
	return preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));  
}

function is_weixin(){
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false){
        return true;
    }else{  
        return false;
	}
}

function get_url($url){ //get
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // 要求结果为字符串且输出到屏幕上
			curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
			curl_setopt($ch, CURLOPT_TIMEOUT, 15);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			$output = curl_exec($ch);
			curl_close($ch);
			//file_put_contents("log.log",$output);
			$result = json_decode($output); //判断JSON
			//wei_log($url,$output);
			return $result;
	}


function post_url($url,$params){ //post
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // 要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_POST, 1);  // post 提交方式
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$output = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($output); //判断JSON
		//wei_log($url,$output);
		return $result;
}

//获取文件后缀
function get_extension($file)
{
	$arr = explode(".",$file);
	return $arr[sizeof($arr) - 1];
}

//把远程文件下载到本地的算法
function down_file($bgimg){
	//如果是远程图片那么要下载到本地
	if(strpos($bgimg,'http')===0){
		$houzui = get_extension($bgimg);
		if($houzui!='jpg' && $houzui!='png') $houzui = 'jpg';//默认jpg
		$bgimg_name = dirname(__FILE__)."/qrcard/file_".md5($bgimg).".".$houzui;
		file_put_contents($bgimg_name,file_get_contents($bgimg));
		$bgimg = $bgimg_name;
	}
	return $bgimg;
}

//根目录的url
function base_url(){
	return 'http://'.$_SERVER['HTTP_HOST'].'/';
}

//隐藏后面几位
function yc_hou($str , $num = 6){
	return substr($str , 0, - $num).'******';
}

//判断是ios还是安卓
function get_device_type()
{
	//全部变成小写字母
	$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	$type = 'other';
	//分别进行判断
	if(strpos($agent, 'iphone') || strpos($agent, 'ipad'))
	{
		$type = 'ios';
	}else if(strpos($agent, 'android'))
	{
		$type = 'android';
	}
	return $type;
}

//object转Array
function objectToArray($obj){
	return json_decode(json_encode($obj),true);
}

//过滤乱码
function filter_utf8_char($ostr){
    preg_match_all('/[\x{FF00}-\x{FFEF}|\x{0000}-\x{00ff}|\x{4e00}-\x{9fff}]+/u', $ostr, $matches);
    $str = join('', $matches[0]);
    if($str==''){   //含有特殊字符需要逐個處理
        $returnstr = '';
        $i = 0;
        $str_length = strlen($ostr);
        while ($i<=$str_length){
            $temp_str = substr($ostr, $i, 1);
            $ascnum = Ord($temp_str);
            if ($ascnum>=224){
                $returnstr = $returnstr.substr($ostr, $i, 3);
                $i = $i + 3;
            }elseif ($ascnum>=192){
                $returnstr = $returnstr.substr($ostr, $i, 2);
                $i = $i + 2;
            }elseif ($ascnum>=65 && $ascnum<=90){
                $returnstr = $returnstr.substr($ostr, $i, 1);
                $i = $i + 1;
            }elseif ($ascnum>=128 && $ascnum<=191){ // 特殊字符
                $i = $i + 1;
            }else{
                $returnstr = $returnstr.substr($ostr, $i, 1);
                $i = $i + 1;
            }
        }
        $str = $returnstr;
        preg_match_all('/[\x{FF00}-\x{FFEF}|\x{0000}-\x{00ff}|\x{4e00}-\x{9fff}]+/u', $str, $matches);
        $str = join('', $matches[0]);
    }
    return $str;
}

//过滤特殊字符
function strFilter($str){
    $str = str_replace('`', '', $str);
    $str = str_replace('·', '', $str);
    $str = str_replace('~', '', $str);
    $str = str_replace('!', '', $str);
    $str = str_replace('！', '', $str);
    $str = str_replace('@', '', $str);
    $str = str_replace('#', '', $str);
    $str = str_replace('$', '', $str);
    $str = str_replace('￥', '', $str);
    $str = str_replace('%', '', $str);
    $str = str_replace('^', '', $str);
    $str = str_replace('……', '', $str);
    $str = str_replace('&', '', $str);
    $str = str_replace('*', '', $str);
    $str = str_replace('(', '', $str);
    $str = str_replace(')', '', $str);
    $str = str_replace('（', '', $str);
    $str = str_replace('）', '', $str);
    $str = str_replace('-', '', $str);
    $str = str_replace('_', '', $str);
    $str = str_replace('——', '', $str);
    $str = str_replace('+', '', $str);
    $str = str_replace('=', '', $str);
    $str = str_replace('|', '', $str);
    $str = str_replace('\\', '', $str);
    $str = str_replace('[', '', $str);
    $str = str_replace(']', '', $str);
    $str = str_replace('【', '', $str);
    $str = str_replace('】', '', $str);
    $str = str_replace('{', '', $str);
    $str = str_replace('}', '', $str);
    $str = str_replace(';', '', $str);
    $str = str_replace('；', '', $str);
    $str = str_replace(':', '', $str);
    $str = str_replace('：', '', $str);
    $str = str_replace('\'', '', $str);
    $str = str_replace('"', '', $str);
    $str = str_replace('“', '', $str);
    $str = str_replace('”', '', $str);
    $str = str_replace(',', '', $str);
    $str = str_replace('，', '', $str);
    $str = str_replace('<', '', $str);
    $str = str_replace('>', '', $str);
    $str = str_replace('《', '', $str);
    $str = str_replace('》', '', $str);
    $str = str_replace('.', '', $str);
    $str = str_replace('。', '', $str);
    $str = str_replace('/', '', $str);
    $str = str_replace('、', '', $str);
    $str = str_replace('?', '', $str);
    $str = str_replace('？', '', $str);
    return trim($str);
}

//php获取当前url
function current_url_201712(){
  $pageURL = 'http';
  if ($_SERVER["HTTPS"] == "on") 
  {
    $pageURL .= "s";
  }
  $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") 
  {
    $pageURL .= $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
  } 
  else
  {
    $pageURL .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
  }
  return $pageURL;
}

function current_url(){
	return current_url_201712();
}

//毫秒级时间戳
function getMillisecond(){
	$time = explode (" ", microtime () );   
	$time = $time [1] . $time [0];  
	$time = str_replace(".","",$time);
	return $time;	
}

/** 
 * 日期时间友好显示 
 * @param $time 
 * @return bool|string 
 */  
function friend_date($time)  
{
    if (!$time) {  
        return false;  
    }  
    $fdate = '';  
    $d = time() - intval($time);  
    $ld = $time - mktime(0, 0, 0, 0, 0, date('Y')); //得出年  
    $md = $time - mktime(0, 0, 0, date('m'), 0, date('Y')); //得出月  
    $byd = $time - mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')); //前天  
    $yd = $time - mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天  
    $dd = $time - mktime(0, 0, 0, date('m'), date('d'), date('Y')); //今天  
    $td = $time - mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')); //明天  
    $atd = $time - mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')); //后天  
    if ($d == 0) {  
        $fdate = '刚刚';  
    } else {  
        switch ($d) {  
            case $d < $atd:  
                $fdate = date('Y年m月d日', $time);  
                break;  
            case $d < $td:  
                $fdate = '后天' . date('H:i', $time);  
                break;  
            case $d < 0:  
                $fdate = '明天' . date('H:i', $time);  
                break;  
            case $d < 60:  
                $fdate = $d . '秒前';  
                break;  
            case $d < 3600:  
                $fdate = floor($d / 60) . '分钟前';  
                break;  
            case $d < $dd:  
                $fdate = floor($d / 3600) . '小时前';  
                break;  
            case $d < $yd:  
                $fdate = '昨天' . date('H:i', $time);  
                break;  
            case $d < $byd:  
                $fdate = '前天' . date('H:i', $time);  
                break;  
            case $d < $md:  
                $fdate = date('m月d日 H:i', $time);  
                break;  
            case $d < $ld:  
                $fdate = date('m月d日', $time);  
                break;  
            default:  
                $fdate = date('Y年m月d日', $time);  
                break;  
        }  
    }  
    return $fdate;  
}
?>