<?php session_start();
include_once dirname(__file__)."/../ewcfg10.php";
include_once dirname(__file__)."/../ewshared10.php";
include_once dirname(__file__)."/../phpfn10.php";
if (isset($_SESSION[EW_SESSION_USER_ID])){
}else{
	die("error == 503，登录超时，为了安全起见，请重新登录！");
}
?>
<?php
if($_FILES['upload']['error'] > 0){  
   echo '!problem:';  
   switch($_FILES['upload']['error'])  
   {  
     case 1: echo '文件大小超过服务器限制';  
             break;  
     case 2: echo '文件太大！';  
             break;  
     case 3: echo '文件只加载了一部分！';  
             break;  
     case 4: echo '文件加载失败！';  
             break;  
   }  

   exit;  
}  
if($_FILES['upload']['size'] > 1000000){
   echo '文件过大！';  
   exit;  
}

$filetype = $_FILES['upload']['type'];  

if($filetype!='image/jpeg' && $filetype!='image/gif' && $filetype != 'image/png'){  
   echo '文件不是JPG,PNG或者GIF图片！';  
   exit;  
}
$today = date("YmdHis");  
if($filetype == 'image/jpeg'){  
  $type = '.jpg';  
}  
if($filetype == 'image/gif'){  
  $type = '.gif';  
}
if($filetype == 'image/png'){  
  $type = '.png';  
}

$upfile = './ckeditor_upfile/' . $today . $type;
  
if(is_uploaded_file($_FILES['upload']['tmp_name']))  
{  
   if(!move_uploaded_file($_FILES['upload']['tmp_name'], $upfile))  
   {  
     echo '移动文件失败！';  
     exit;  
    }  
}  
else  
{  
   echo 'problem!';  
   exit;  
}  
/*
echo '<h1>success!</h1><br>';   
echo '文件大小：' . $_FILES['upload']['size'] . '字节' . '<Br>';  
echo '文件路径：' . $upfile;  
echo '<hr with="100%" />' . ' ';  
$dirr = 'upfile/';  
$dir = opendir($dirr);  
echo $dirr . '--Listing:<ul>';  
while($file = readdir($dir)){  
  echo "<li>$file</li>";  
}  
echo '</ul>'; */ 

$callback = $_GET["CKEditorFuncNum"];
echo ("<script type=\"text/javascript\">");    
echo("window.parent.CKEDITOR.tools.callFunction(" . $callback . ",'" . dirname('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]).'/'.$upfile . $file . "','')");    
echo("</script>");  
//closedir($dir);  
?>