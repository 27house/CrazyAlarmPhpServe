<?php session_start();
include_once dirname(__file__)."/../ewcfg10.php";
include_once dirname(__file__)."/../ewshared10.php";
include_once dirname(__file__)."/../phpfn10.php";
if (isset($_SESSION[EW_SESSION_USER_ID])){
}else{
	die("error == 503����¼��ʱ��Ϊ�˰�ȫ����������µ�¼��");
}
?>
<?php
if($_FILES['upload']['error'] > 0){  
   echo '!problem:';  
   switch($_FILES['upload']['error'])  
   {  
     case 1: echo '�ļ���С��������������';  
             break;  
     case 2: echo '�ļ�̫��';  
             break;  
     case 3: echo '�ļ�ֻ������һ���֣�';  
             break;  
     case 4: echo '�ļ�����ʧ�ܣ�';  
             break;  
   }  

   exit;  
}  
if($_FILES['upload']['size'] > 1000000){
   echo '�ļ�����';  
   exit;  
}

$filetype = $_FILES['upload']['type'];  

if($filetype!='image/jpeg' && $filetype!='image/gif' && $filetype != 'image/png'){  
   echo '�ļ�����JPG,PNG����GIFͼƬ��';  
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
     echo '�ƶ��ļ�ʧ�ܣ�';  
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
echo '�ļ���С��' . $_FILES['upload']['size'] . '�ֽ�' . '<Br>';  
echo '�ļ�·����' . $upfile;  
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