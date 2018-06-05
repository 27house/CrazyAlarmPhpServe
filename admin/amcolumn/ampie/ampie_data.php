<?php 
// create doctype 
$dom = new DOMDocument("1.0"); 
// display document in browser as plain text 
// display document in browser as plain text 
// for readability purposes 
header("Content-Type: text/plain"); 
// save and display tree 
echo $dom->saveXML(); 
?>
<pie>
  <!--
  <message bg_color="#CCBB00" text_color="#FFFFFF">
    <![CDATA[You can broadcast any message to chart from data XML file]]>
  </message>
  -->
  <?php
  $title_list = $_GET['title_list'];
  $count_list = $_GET['count_list'];
  $title_arr = explode(',',$title_list);
  $count_arr = explode(',',$count_list);
  for ($i=0;$i<sizeof($title_arr);$i++)
  {
  	?>
		 <slice title="<?php echo iconv('gb2312','utf-8',$title_arr[$i]);?>" <?php 
		 if ($i==0)
		 {
		 	echo 'pull_out="true"';
		 }
		 ?> ><?php echo $count_arr[$i];?></slice>
	<?php
  }
  ?>

</pie>
