<?php
include_once dirname(__FILE__)."/../admin/z_lxl_conn.php";//连接数据库
$method = $_GET['method'];

/**
 * 查询用户，并拼接用户头像访问地址
 * $db 必须要传进去
 */
function getUser($account,$db){
	$sql = "select * from `user_info` where account='$account' limit 1";
	$user = $db->get_row($sql);
	if(!empty($user)){
		$user->avatar=HTTP_ROOT."upload/".$user->avatar;
	}
	return $user;
}

// 定义返回数据
$arr = array();
switch($method){
	case "login":
		// 登录
		$account=str_replace(" ","",$_POST['account']);//接收客户端发来的username；
		$user = getUser($account,$db);
		if(!empty($user)){
			if($_POST['password']==$user->password){
					$login_time = date("Y-m-d h:i:sa");
					$u_arry = array();
					$u_arry['login_time'] = $login_time;
					update_arr2($u_arry, "user_info", "`account` = '{$account}'");
					
					$user->login_time=$login_time;
					$arr['result'] = 0;
					$arr['data'] = $user;
					$arr['message'] = '登录成功！';
			}else{
					$arr['result'] = -1;
					$arr['message'] = '密码不正确！';
				}
		}else{
			$arr['result'] = -2;
			$arr['message'] = '请先注册！';
		}
	break;
	case "register":
		// 注册
		
		$account=str_replace(" ","",$_POST['account']);//接收客户端发来的username；
		$user = getUser($account,$db);
		if(!empty($user)){
			
			$arr['result'] = -2;
			$arr['message'] = '该账号已被注册！';
		}else{
	
			$password=str_replace(" ","",$_POST['password']);
			$u_arry = array();
			//substr($account,0,stripos($account,"@"))
			$u_arry['nickname'] = "用户".rand();
			$u_arry['account'] = $account;
			$u_arry['password'] = $password;
			$u_arry['describe'] = "";
			$u_arry['create_time'] = date("Y-m-d H:i:s");
			insert_arr($u_arry, "user_info");
			
			$arr['result'] = 0;
			$arr['message'] = '注册成功！';
		}
		
	break;
	case "avatar":
		// 修改头像路径
		if(!empty($_POST)){
				//获取用户id
				$acc = $_REQUEST['account'];
	
				if(!empty($acc)){
					
					$s = dirname(__FILE__) . "/../upload/"; //获的服务器路劲
					$user_tx = md5(md5($acc.rand()));//获取当前用户头像,头像文件名加密
					$files = $_POST['files1'];
//					file_put_contents(dirname(__FILE__)."/up.txt",json_encode($_POST));
//					$files1 = substr($files,1,22);  //百度一下就可以知道base64前面一段需要清除掉才能用。
					$files1 = substr(strstr($files, ','), 1);
					//解码
					$tmp = base64_decode($files1);
					$fp = $s . $user_tx . ".jpg";  //确定图片文件位置及名称
					//写文件
					file_put_contents($fp, $tmp);  //给图片文件写入数据
					$img_url = $user_tx . ".jpg";  //get_cache_img($img_head . $user_tx . ".jpg",200,1,0);
					
					// 删除原文件
					$user = getUser($acc,$db);
					unlink (HTTP_ROOT."upload/".$user->avatar);
					// 修改数据库值
					$u_arry = array();
					$u_arry['avatar'] = $img_url;
					update_arr2($u_arry, "user_info", "`account` = '{$acc}'");
					// 查找整条数据并返回
					$user = getUser($acc,$db);
					
					$arr['result'] = 0;
					$arr['message']= "修改头像成功！";
				}else{
					$arr['result'] = -1;
					$arr['message'] = "修改头像失败！";
				}
				
	        }else{
	            $arr['result'] = -2;
				$arr['message'] = "参数空！";
	        }
		
	break;
	case "update":
		$account=str_replace(" ","",$_POST['account']);//接收客户端发来的username；
		$type = $_POST['type'];
		$user = getUser($account,$db);
		if(!empty($user)){
			$u_arry = array();
			if($type == 0){
				// 昵称
				$u_arry['nickname'] = $_POST['value'];
			}else if($type == 1){
				// 性别
				$u_arry['sex'] = $_POST['value'];
			}else if($type == 2){
				// 密码
				$u_arry['password'] = $_POST['value'];
			}else if($type == 3){
				// 签名
				$u_arry['describe'] = $_POST['value'];
			}
			update_arr2($u_arry, "user_info", "`account` = '{$account}'");
			$arr['result'] = 0;
			$arr['message']= "修改成功！";
		}else{
			$arr['result'] = -1;
			$arr['message']= "用户不存在！";
		}
	break;
	case "get_user":
		$account = str_replace(" ","",$_GET['account']);//接收客户端发来的username；
		$user = getUser($account,$db);
		$arr['result'] = 0;
		$arr['data'] = $user;
		$arr['message']= "";
	break;
	case "get_user_list":
		$sql = "select * from `user_info`";
		$userlist = $db->get_results($sql);
		if(!empty($userlist)){
			foreach($userlist as $user){
				$user->avatar=HTTP_ROOT."upload/".$user->avatar;
			}
		}
		$arr['result'] = 0;
		$arr['data'] = $userlist;
		$arr['message']= "";
	break;

}
// 发送数据
echo json_encode($arr);

?>