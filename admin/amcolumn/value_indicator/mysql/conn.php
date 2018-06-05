<?php
// Include ezSQL core
	include_once dirname(__file__)."/../shared/ez_sql_core.php";

	// Include ezSQL database specific component
	include_once dirname(__file__)."/ez_sql_mysql.php";
	
	include_once dirname(__file__)."/../../../ewcfg10.php";
	
	// Initialise database object and establish a connection
	// at the same time - db_user / db_password / db_name / db_host
	$db = new ezSQL_mysql(EW_CONN_USER,EW_CONN_PASS,EW_CONN_DB,EW_CONN_HOST.':'.EW_CONN_PORT);
	$db->query("set names gbk");
?>