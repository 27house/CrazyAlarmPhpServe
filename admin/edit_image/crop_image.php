<?php
$img_list=$_REQUEST['img'];
if($img_list==""){
	echo "图片没找到！";
	exit;
}
$img_arr = explode(',',$img_list);
$img_size = sizeof($img_arr);
$path = htmlspecialchars($_GET['path']);
$w = htmlspecialchars($_GET['w']);
$h = htmlspecialchars($_GET['h']);
$img=$path.'/'.$img_arr[0];

//预览图的宽度和高度
$slw = 200;
$slh = (($slw*$h)/$w);

//画布的宽度和高度
$bw = 1000;
$bh = (($bw*$h)/$w);

//选择框的宽度和高度
$sw = $bw/2;
$bh = (($sw*$h)/$w);

$callback="show_head";//artdilog callback
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" /> 
<title>裁剪图片</title>
<script src="js/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="js/jquery.Jcrop_new.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/jquery.Jcrop.min.css" type="text/css" />
<script type="text/javascript" src="js/artDialog4.1.6/jquery.artDialog.js?skin=default"></script>
<script type="text/javascript" src="js/artDialog4.1.6/plugins/iframeTools.js"></script>

<!--<link rel="stylesheet" href="__PUBLIC__/Css/jcrop.css" type="text/css" />-->
<style type="text/css">
/* CSS Document */
body{padding:0; margin:0; font-size:83%; background:#eeeeee; color:#333333; font-family:'宋体',Verdana, Geneva, sans-serif;}
.rel{position:relative;}.abs{position:absolute;}.fixed{position:fixed;}
.zxx_out_box{margin:0 auto;}
.zxx_main_con{padding:0 20px 20px;}
.zxx_test_list{padding:1em; font-size:1.1em;line-height:1.3; overflow:hidden; zoom:1;}
.love_img_btn{ text-align:center; margin-top:30px;}
.btn2{ font-size:12px; color:#FFF; text-align:center; width:79px; height:30px; line-height:30px; border:none; background:url(../style/images/btn_bg1.png) no-repeat;}
.jcrop-holder { float:left;}
/*控制预览图大小*/
.crop_preview{float:left;margin-left:20px;width:<?php echo $slw;?>px;height:<?php echo $slh;?>px;overflow:hidden;}
.clear{clear:both}
.btn{cursor:pointer}
.tl{text-align:left}
.tc{text-align:center}
.ml10 {margin-left:10px;}
.mt10 {margin-top:10px;}

</style>
<script type="text/javascript">
	$(document).ready(function(){
		xsize = '<?php echo $w;?>',
        ysize = '<?php echo $h;?>';
		$("#xuwanting").Jcrop({
			allowSelect:false,
			allowResize:true,
			onChange:showPreview,
			onSelect:showPreview,
			aspectRatio: xsize / ysize,
	        setSelect: [ 0, 0, <?php echo $sw;?>, <?php echo $sh;?> ],	       
	        boxWidth:<?php echo $bw;?>,
	        boxHeight:<?php echo $bh;?>
			
		});	
		//简单的事件处理程序，响应自onChange,onSelect事件，按照上面的Jcrop调用
		function showPreview(coords){
			if(parseInt(coords.w) > 0){
				//计算预览区域图片缩放的比例，通过计算显示区域的宽度(与高度)与剪裁的宽度(与高度)之比得到
				var rx = $("#preview_box").width() / coords.w; 
				var ry = $("#preview_box").height() / coords.h;
				//通过比例值控制图片的样式与显示
				$("#crop_preview").css({
					width:Math.round(rx * $("#xuwanting").width()) + "px",	//预览图片宽度为计算比例值与原图片宽度的乘积
					height:Math.round(rx * $("#xuwanting").height()) + "px",	//预览图片高度为计算比例值与原图片高度的乘积
					marginLeft:"-" + Math.round(rx * coords.x) + "px",
					marginTop:"-" + Math.round(ry * coords.y) + "px"
				});
				$('#x').val(coords.x);
		        $('#y').val(coords.y);
		        $('#w').val(coords.w);
		        $('#h').val(coords.h);
			}
		}
		//表单提交
		$("#confirmOk").click(function(){			
			if (parseInt($('#w').val())){
				var act = $("#act").val();
				var pic_name = $("#pic_name").val();
				var x = $("#x").val();
				var y = $("#y").val();
				var w = $("#w").val();
				var h = $("#h").val();
				
				$.post("crop_submit.php",{pic_name:pic_name,x:x,y:y,w:w,h:h,path:"<?php echo $path;?>"},function(data){
											
					if(data.status==1){
						<?php
							if($img_size == 1){
								?>
								alert('裁剪成功！');
								$(window.parent.document).find("div[class=xubox_layer]").remove();
								<?php
							}else{
								?>
								alert('指定图片裁剪成功！');
								<?php
							}
						?>
						//set_img(pic_name);
						//location.reload();//刷新
						/*
						var _call_back="<?php echo $callback;?>";
						var win = art.dialog.open.origin; 
						if(_call_back && win[_call_back] && typeof(win[_call_back])=='function'){ 
							try{ 
								
								//win[_call_back].call(this, data.file); 
								//art.dialog.close();
							}catch(e){
								alert('确认异常');			
							}; 
						} 
						*/
					}else{
					
						alert(data.error);
						return false;
					}					
					
				},"json");
			    return false;
			}			
		    return false;
		});
	});
</script>
</head>
<body>
<style>
ul,li,div{ margin:0px; padding:0px; border:0px;}
div.img_list_all{
width:100%; float:left;border-bottom: 1px #ccc solid;padding-bottom: 30px;
overflow-y: auto;
height: 60px;
}
div.img_list_all ul{
width: 130px; float:left; height:60px;text-align: center; cursor:pointer;margin-bottom: 30px;}
div.img_list_all li{ list-style:none; width:100%; text-align:center; overflow:hidden; margin-top: 5px; margin-right: 5px;}
.tc{
position: fixed;
bottom: 0px;
left: 0px;
width: 100%;
background: #cccccc;
}
body{
overflow: hidden;
}
.zxx_test_list{
overflow: scroll!important;
height: 310px;
width: 752px;
}
.zxx_out_box{
	float: left;
	margin: 0 auto;
    width: 100%;
}
</style>

<?php
if ($img_size > 0){
?>
<div class="img_list_all">
<?php
foreach($img_arr as $k=>$v){
?>
	<ul id="<?php echo $v;?>">
		<li><img src="<?php echo $path.'/'.$v?>" width="80%" height="50" style="padding:3px;border:1px solid #ccc;" /></li>
		<li>图片<?php echo ($k+1);?></li>
	</ul>
<?php
}
?>
</div>
<?php
	
}
?>
<script>
function set_img($img){
	$this_pic = $img;
	$("div[class=jcrop-holder] img").attr("src",$this_pic);
	$("img[id=xuwanting]").attr("src",$this_pic);
	$("img[id=crop_preview]").attr("src",$this_pic);
	$("input[id=pic_name]").attr("value",$this_pic);
}
	$("div.img_list_all ul").click(function(){
		$img = $(this).attr("id");
		$this_pic = "<?php echo $path.'/';?>"+$img;
		set_img($this_pic);
	});
</script>

<div class="zxx_out_box">
    <div class="zxx_main_con">
        <div class="zxx_test_list">
            <div class="rel">
                <img id="xuwanting" src="<?php echo $img;?>" style="display: none;"/>
                <span id="preview_box" class="crop_preview"><img id="crop_preview" src="<?php echo $img;?>"/></span>
            </div>
        </div>
    </div>
    <div class="zxx_footer">
        <form>
  	        <input type="hidden" id="act" name="act" value="background"/>
  			<input type="hidden" id="pic_name" name="pic_name" value="<?php echo $img;?>"/>
			<input type="hidden" id="x" name="x" />
			<input type="hidden" id="y" name="y" />
			<input type="hidden" id="w" name="w" />
			<input type="hidden" id="h" name="h" />			
	    </form>
     </div>
</div>
<div class="clear"></div>
   <div class="fn pt20 tc">
      <input type="button" class="btn-all w110 ml-180" id="confirmOk" value="确定裁剪" style="padding: 5px 15px;float: right;margin-right: 20px;" />
	</div>

	</body>
</html>
