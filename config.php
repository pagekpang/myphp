<?php

define("DEBUG", 1);
session_start();

define('ROOT_PATH', str_replace('config.php', '', str_replace('\\', '/', __FILE__)));

require 'includes/dz_debug.php';
global $log;
$log = new Debug();
$log->i("started log system.", 'system', __FILE__, __LINE__);

function DisplayError($msg)
{
	global $log;
	$log->i("called DisplayError for :".substr($msg, 0,4), 'system', __FILE__, __LINE__);
	$emsg = '';
	$emsg .= "<div><h3>Dz Error Warning!</h3>\r\n";
	$emsg .= "<div><a href='#' target='_blank' style='color:red'>Technical Support: QQ:304975517</a></div>";
	$emsg .= "<div style='line-helght:160%;font-size:14px;color:green'>\r\n";
	$emsg .= "<div style='color:blue'><br />Error page: <font color='red'>".$_SERVER['PHP_SELF']."</font></div>\r\n";
	$emsg .= "<div>Error infos: ".$msg."</div>\r\n";
	$emsg .= "<br /></div></div>\r\n";
	echo $emsg;
}


global $db;
global $dzsql;
require 'includes/dz_mysql.php';
$dzsql = $db = new DzSql('192.168.2.19', 'www', 'www.', 'test','utf8');

