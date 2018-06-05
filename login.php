<?php
include_once dirname(__FILE__)."/ezsql/conn.php";//连接数据库
$account=str_replace(" ","",$_POST['account']);//接收客户端发来的username；

$sql = "select * from `user_info` where account='$account' limit 1";
$user = $db->get_row($sql);
if(!empty($user)){
	if($_POST['password']==$user->password){
			echo "登录成功";
		}else{
		echo "密码错误";
		}
}else{
	echo "没有数据";
}
?>