<?php
$method = $_GET['method'];
switch($method){
	case 'update_img':
		if(!empty($_POST)){
				//获取用户id
				$account = $_REQUEST['account'];
	
				if(!empty($uid)){
					$s = dirname(__FILE__) . "/../upload/"; //获的服务器路劲
					$user_tx = "user_tx".$account."_".md5(md5("201710231928".$account));//获取当前用户头像,头像文件名加密
					$files = $_POST['files1'];
					//file_put_contents(dirname(__FILE__)."/up.txt",json_encode($_POST));
					//$files1 = substr($files,1,22);  //百度一下就可以知道base64前面一段需要清除掉才能用。
					$files1 = substr(strstr($files, ','), 1);
					//解码
					$tmp = base64_decode($files1);
					$fp = $s . $user_tx . ".jpg";  //确定图片文件位置及名称
					//写文件
					file_put_contents($fp, $tmp);  //给图片文件写入数据
					$img_url = get_cache_img($img_head . $user_tx . ".jpg",200,1,0);
					
					
					
					
					$re['result'] = 0;
					$re['url'] = $img_url."?t=".time();
					$re['message']= "上传成功！";
				}else{
					$re['result'] = -1;
					$re['message'] = "上传失败！";
				}
				
	        }else{
	            $re['result'] = -2;
				$re['message'] = "上传失败！";
	        }
        break;
}
?>