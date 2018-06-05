<?php
  $title_list = $_GET['title_list'];
  $count_list = $_GET['count_list'];
  $title_arr = explode(',',$title_list);
  $count_arr = explode(',',$count_list);
  for ($i=0;$i<sizeof($title_arr);$i++)
  {
  	if ($i<(sizeof($title_arr)-1))
	{
  	echo iconv('gb2312','utf-8',$title_arr[$i]).";".$count_arr[$i]."\n";
	}
	else
	{
	echo iconv('gb2312','utf-8',$title_arr[$i]).";".$count_arr[$i];
	}
  }
?>