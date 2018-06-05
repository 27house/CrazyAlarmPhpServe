<?php
if (!ew_IsMobile()){
?>
<link href="<?php echo cache_cdn('assets/css/bootstrap.min.css');?>" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo cache_cdn('assets/css/font-awesome.min.css');?>" />
<!--[if IE 7]>
	  <link rel="stylesheet" href="<?php echo cache_cdn('assets/css/font-awesome-ie7.min.css');?>" />
<![endif]-->
<link rel="stylesheet" href="<?php echo cache_cdn('assets/css/ace.min.css');?>" />

<link href="<?php echo cache_cdn('assets/admin.css');?>" rel="stylesheet" />

<script src="<?php echo cache_cdn('assets/admin.js');?>"></script>

<script src="<?php echo cache_cdn('layer/layer.min.js');?>"></script>

<?php
}else{
//手机相关的js
	
		?>
		<script>
		$(document).ready(function(){
				$("div#sidebar").hide();
				$(".breadcrumb").hide();
				$(".ewSiteTitle").hide();
				$(".form-inline").hide();
				$(".ewListExportOptions").hide();
			});
		</script>
		<?php
	
}
?>