<script>
	$("#navbar").hide();
	$("#sidebar").hide();
	$(".ewStdTable").hide();
	$("body").attr("class","login-layout");
	$(".main-content").attr("class","main-content2");
	//$("#com_name_div").html("&copy; 银星时代科技有限公司");
	$("#login_title").html("请输入用户名和密码");
	$("#username").attr("placeholder","用户名");
	$("#password").attr("placeholder","密码");
	$("#login_span").html("立即登录");
	$("#lbl").html("记住我");
	
	$(document).ready(function(){
		$("a[href='register.php']").remove();
		//$("span[id=lbl]").after("<br /><a href='register.php' style='font-size:14px;text-indent:2em;'>注册</a>");
	});
	
	<?php
		if($_POST){
			?>
			$(".ewStdTable").attr("id","ewStdTable");
		$(".ewStdTable").attr("class","alert alert-danger");
		$("table[id=ewStdTable]").show();	
			<?php
			}
	?>
	
	$(".login-container").prev().hide();	
	
</script>
<style>
	body{
		background-color: rgb(29, 32, 36);
	}
	
	.main-content2{
			width:375px;
			height:381px;
			margin:0 auto;
	}
</style>