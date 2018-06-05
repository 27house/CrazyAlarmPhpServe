<?php
$pic_name=$_REQUEST['pic_name'];
$x=$_REQUEST['x'];
$Y=$_REQUEST['Y'];
$w=$_REQUEST['w'];
$h=$_REQUEST['h'];
$targ_w = $w;
$targ_h = $h;
include_once("jcrop_image.class.php");
$filep=$_REQUEST['path']."/";
$crop=new jcrop_image($filep, $pic_name,$x,$y,$w,$h,$targ_w,$targ_h);
$file=$crop->crop();

?>