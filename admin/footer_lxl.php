<?php

if (!ew_IsMobile()){
?>
<!-- basic scripts -->
		<!-- page specific plugin scripts -->

		<!-- ace scripts -->

		<script src="<?php echo cache_cdn('assets/js/ace-elements.min.js');?>"></script>
		<script src="<?php echo cache_cdn('assets/js/ace.min.js');?>"></script>

		<!-- inline scripts related to this page -->

<script src="<?php echo cache_cdn('./jquery/jquery.cookie.js');?>"></script>

<script>
//如果本页被框架了，则不显示右边的导航  
if(top.location != location){  
	$(document).ready(function(){
		$("div[id=navbar]").remove();
		$("div[id=sidebar]").remove();
		$("ul[class=breadcrumb]").parents('table').remove();
		$("div[class=main-content]").css("margin-left","0px");	
		$("form[id=fweixin_order_detaillistsrch]").hide(); //搜索框也去掉
		$("form[id=fjf_loglistsrch]").hide(); //搜索框也去掉
		$("form[id=fxf_loglistsrch]").hide(); //搜索框也去掉
		$("form[id=fcv_weixin_order_detail2listsrch]").hide(); //搜索框也去掉
		$("form[id=fweixin_item_orderlistsrch]").hide(); //搜索框也去掉
		$("div[class=main-content]").css("height","auto!important");
		$("table[class=ewGrid]").css("margin-top","10px");	
	});
}

$(document).ready(function(){
	
	//遍历所有菜单
	$anum = $('#RootMenu a').length;
	for ($i=0;$i<$anum;$i++)
	{
		if ((typeof($('#RootMenu a:eq('+$i+')').attr("href")) == "undefined") || ($('#RootMenu a:eq('+$i+')').attr("href") == "#"))
		{
			$('#RootMenu a:eq('+$i+')').attr("href","javascript:;");
			
		}
	}
	
	//遍历所有顶级菜单
	$anum = $('#RootMenu').children("li").children("a").length;
	$mi = 0;
	for ($i=0;$i<$anum;$i++)
	{
		
		$('#RootMenu').children("li").children("a:eq("+$i+")").html('<span class="menu-text"> '+$('#RootMenu').children("li").children("a:eq("+$i+")").html()+"</span>");
		$('#RootMenu').children("li").children("a:eq("+$i+")").append('<b class="arrow icon-angle-down"></b>');
		$('#RootMenu').children("li").children("a:eq("+$i+")").attr("class","dropdown-toggle");
		$('#RootMenu').children("li").children("a:eq("+$i+")").next("ul").attr("class","submenu");
		
		$('#RootMenu').children("li").children("a:eq("+$i+")").next("ul").find("a").prepend('<i class="icon-double-angle-right"></i>');
		
		$mi ++;
		$('#RootMenu').children("li").children("a:eq("+$i+")").attr('bh','b'+$mi);
		$('#RootMenu').children("li").children("a:eq("+$i+")").attr('t1',"t1");
	}
	//默认第一个菜单打开
	if(($.cookie('oe')!="") && ($.cookie('oe')!=null)){
		$('a[bh="'+$.cookie('oe')+'"]').parent().addClass("open");
		$('a[bh="'+$.cookie('oe')+'"]').next().show();
	}else{
		$('#RootMenu').children("li").children("a:eq(0)").parent().addClass("open");
		$('#RootMenu').children("li").children("a:eq(0)").next().show();
	}
	
	
	$('a[t1=t1]').click(function(){
		if ($(this).next().is(":visible"))
		{
			$.cookie('oe',"");
		}else
		{	
			$.cookie('oe',$(this).attr("bh"));
		}
	});
	
	ll_tz();
	$(window).resize(function() {
		ll_tz();	
	});	
	
	//调整大小函数
	function ll_tz()
	{
		//alert($(document.body).height());
		//调整大小
		$('#sidebar').css('height',$(document).height()-$('#navbar').height());
		$('.main-content').css('height',$(document).height()-$('#navbar').height());
		
		$(".breadcrumbs").width($(document.body).width()-$('#sidebar').width() - 14);
		
		//alert($('#sidebar').height());
		
		//滚动条位置
		//$("#bodyall2").scrollTop($.cookie('me_le_height'));
		//$('#bodyall2').scrollLeft($.cookie('me_le_left'));
	
	}
	//下面是操作系统自带的
	
	$(".ewSiteTitle").remove();
	$(".divider").remove();
	
	$("ul[class=breadcrumb]").children("li:eq(0)").prepend('<i class="icon-home home-icon"></i>');
	$("ul[class=breadcrumb]").parent().parent().parent().parent().addClass("breadcrumbs");
	
	$(".ewBasicSearch").children("div:last").prev().css("clear","both");
	$(".ewBasicSearch").children("div:last").addClass("xsr_last");
	$(".ewBasicSearch button[id=btnsubmit]").prepend('<i class="icon-search icon-on-right bigger-110"></i>  ');
	
	//$(".ewBasicSearch a.btn").removeClass("btn");
	
	$(".ewBasicSearch div:last").hide();
	
	$(".widget-header").next("div").addClass("widget-body");
	
	//$(".ewGrid").attr("class","table table-striped table-bordered table-hover");
	//$("a.btn").removeClass("btn");
	
	//如果没有设置搜索，就把搜索外面的框删掉
	if($("input#psearch").length==0){
		$("form[id=fdh_configlistsrch]").remove();
	}
	
	
	<?php
	global $ico_arr_lxl;
	
	foreach($ico_arr_lxl as $k=>$v){
		?>
		$('#RootMenu').children("li").children("a:eq(<?php echo $k;?>)").prepend('<i class="<?php echo $v;?>"></i>');
		<?php
	}
	?>
	
	$("li.light-blue").hover(function(){
		$(this).find(".user-menu").show();
	},function(){
		$(this).find(".user-menu").hide();
	});
	
	$('ul[id=RootMenu] a[href="logout.php"]').parent().hide();
	$('ul[id=RootMenu] a[href="changepwd.php"]').parent().hide();
	
	$("div.ewViewOtherOptions a").attr("class","btn btn-minier btn-yellow");
	
	$("table.ewGrid button,.ewViewOtherOptions div.btn-group button").removeClass("btn");
	
	$("div.widget-header a.accordion-toggle").attr("href","javascript:;");
	
	$('img[src="phpimages/calendar.png"]').parent().removeClass("btn");
	$('img[src="phpimages/calendar.png"]').parent().css("height","auto");
	$('.dropdown-toggle').removeClass("btn");

	$("a.ewAdd").html('<i class="icon-pencil align-top bigger-125"></i> '+$("a.ewAddEdit").html()+" ");
	$("a.ewMultiDelete").html('<i class="icon-trash align-top bigger-125"></i> '+$("a.ewMultiDelete").html()+" ");
	
	$("ul.dropdown-menu a").removeAttr("class");

	//$("a[data-action=list]").unbind("click","ew_Click");
	
	$("a[data-action=list]").click(function(){
		 window.location.href=$(this).attr("href");
	});
	
	
	//批量找到可以裁剪的图片（方法一）
	$cj_len = $("cj").length;
	for($i=0;$i<$cj_len;$i++){
		$cj_t = $("cj").eq($i); // 获取当前
		$cj_w = $cj_t.attr('w'); //裁剪图片的宽度
		$cj_h = $cj_t.attr('h'); //裁剪图片的高度
		$cj_pic = $cj_t.parent().parent().next().children("div");
		$img_list = $cj_pic.find("input[type=hidden]").eq(0).val();
		$href = "./edit_image/crop_image.php?img="+$img_list+"&path=<?php echo '../'.EW_UPLOAD_DEST_PATH;?>&w="+$cj_w+"&h="+$cj_h;
		if($img_list!=""){
			$cj_pic.children("span").append("建议图片大小："+$cj_w+"*"+$cj_h+"<a href='"+$href+"' cj='t' ow='600' oh='500' title='裁剪图片'>[立即裁剪]</a>");
		}else{
			$cj_pic.children("span").append("建议图片大小："+$cj_w+"*"+$cj_h);
		}
	}
	
	$('a[cj=t]').click(function(){
		$cj_t = $(this).parent().parent().parent().prev().find("cj"); //节点地方
		$cj_w = $cj_t.attr('w'); //裁剪图片的宽度
		$cj_h = $cj_t.attr('h'); //裁剪图片的高度
		$cj_pic = $(this).parent().parent().parent();
		$img_list = $cj_pic.find("input[type=hidden]").eq(0).val();
		$href = "./edit_image/crop_image.php?img="+$img_list+"&path=<?php echo '../'.EW_UPLOAD_DEST_PATH;?>&w="+$cj_w+"&h="+$cj_h;
		//$(this).attr("href",$href);
		show_iframe($href,'图片裁剪',800,500);
		return false;
	});
	
	//获取文件名(裁剪图片时要用到)
	function getFileName(o){
			var pos=o.lastIndexOf("/");
			return o.substring(pos+1);  
	}
	
	//对图片进行裁剪（方法二）
	$('img[cj=1]').attr("title","单击图片可裁剪大小");
	$('img[cj=1]').css("cursor","pointer");
	//$('img[cj=1]').parent().append("<a img_list style='text-align:center;display:block;width:100%;cursor:pointer;'><裁剪大小></a>");
	$('img[cj=1]').click(function(){
		$img_list = getFileName($(this).attr("src"));
		$cj_w = $(this).attr('w'); //裁剪图片的宽度
		$cj_h = $(this).attr('h'); //裁剪图片的高度
		$href = "./edit_image/crop_image.php?img="+$img_list+"&path=<?php echo '../'.EW_UPLOAD_DEST_PATH;?>&w="+$cj_w+"&h="+$cj_h;
		show_iframe($href,'图片裁剪',800,500);
	});
	
	var isIE = /msie/.test(navigator.userAgent.toLowerCase()); //判断是否是ie浏览器
	
	function show_iframe($tourl,$totitle,$ow,$oh){
		var iWidth=window.screen.availWidth/1.3; //弹出窗口的宽度;
		var iHeight=window.screen.availHeight/1.3; //弹出窗口的高度;
		var iTop = (window.screen.availHeight-30-iHeight)/2; //获得窗口的垂直位置;
		var iLeft = (window.screen.availWidth-10-iWidth)/2; //获得窗口的水平位置;
		if($ow>0){
			iWidth = $ow;
		}
		if($oh>0){
			iHeight = $oh;
		}
		if(isIE == false){
			$.layer({
				type: 2,
				shade: [0],
				fix: false,
				title: $totitle,
				maxmin: false,
				iframe: {src : $tourl},
				area: [iWidth+'px' , iHeight+'px'],
				close: function(index){
					//layer.msg('您获得了子窗口标记：' + layer.getChildFrame('#name', index).val(),3,1);
				}
			});
		}else{
			window.open($(this).attr("href"), '', 'resizable=yes,status=yes,location=no,toolbar=no,menubar=no,fullscreen=no,scrollbars=no,dependent=no,width='+iWidth+',height='+iHeight+',left='+iLeft+',top='+iTop); 
		}
		//return false;
	}
	
	
	$('a[o=t]').click(function(){
		
		var iWidth=window.screen.availWidth/1.3; //弹出窗口的宽度;
		var iHeight=window.screen.availHeight/1.3; //弹出窗口的高度;
		var iTop = (window.screen.availHeight-30-iHeight)/2; //获得窗口的垂直位置;
		var iLeft = (window.screen.availWidth-10-iWidth)/2; //获得窗口的水平位置;
		
		if($(this).attr("ow")!=""){
			iWidth = $(this).attr("ow");
		}
		if($(this).attr("oh")!=""){
			iHeight = $(this).attr("oh");
		}
		$tourl = $(this).attr("href");
		$totitle = $(this).attr("title");
		show_iframe($tourl,$totitle,$ow = iWidth,$oh = iHeight);
		return false;
	});
	$("a.ewGridEdit").html("<i class='icon-external-link bigger-125'></i> 批量编辑");
	$("a.ewGridAdd").html("<i class='icon-fire bigger-125'></i> 批量添加");
	
	//ewGrid中，如果有二个分页，则隐藏掉第一个
	$ew_fy_len = $("table[class='ewGrid'] form[name=ewPagerForm]").length;
	if($ew_fy_len == 2){
		$("table[class='ewGrid'] form[name=ewPagerForm]").eq(0).remove();
	}
});
</script>
<?php
}
?>