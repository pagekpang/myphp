<?php
define('DEBUG_D', 'd.txt');
define('DEBUG_E', 'e.txt');
define('DEBUG_I', 'i.txt');
define('DEBUG_A', 'all.txt');
class Debug
{
	var $file_d = null;
	var $file_e = null;
	var $file_i = null;
	var $file_a = null;
	function Debug()
	{
		$this->__construct();
	}
	function __construct()
	{
		if (defined("DEBUG"))
		{
			$this->file_d = fopen(ROOT_PATH . 'log/' .DEBUG_D, 'w');
			$this->file_e = fopen(ROOT_PATH . 'log/' .DEBUG_E, 'w');
			$this->file_i = fopen(ROOT_PATH . 'log/' .DEBUG_I, 'w');
			$this->file_a = fopen(ROOT_PATH . 'log/' .DEBUG_A, 'w');
			if($this->file_d==false||$this->file_e==false||$this->file_i==false||$this->file_a==false)
				die('log error!');
			$emsg = sprintf("%-20s%-50s%-5s%-15s%s\n","Time","File","Line","Tag","Msg");
			fwrite($this->file_d, $emsg);
			fwrite($this->file_e, $emsg);
			fwrite($this->file_i, $emsg);
			$emsg = sprintf("%-20s%-8s%-50s%-5s%-15s%s\n","Time","Level","File","Line","Tag","Msg");
			fwrite($this->file_a, $emsg);
		}
	}
	function __destruct()
	{

		if (defined("DEBUG"))
		{
			fclose($this->file_d);
			fclose($this->file_e);
			fclose($this->file_i);
			fclose($this->file_a);
		}
	}
	function d($msg,$tag,$file,$line)
	{
		if (defined("DEBUG"))
		{
			$emsg = '';
			$emsg = sprintf("%-20s%-50s%-5s%-15s%s\n",$this->get_microtime(),$file,$line,$tag,$msg);
			fwrite($this->file_d, $emsg);
			$emsg = sprintf("%-20s%-8s%-50s%-5s%-15s%s\n",$this->get_microtime(),'Debug',$file,$line,$tag,$msg);
			fwrite($this->file_a, $emsg);
		}
	}
	function e($msg,$tag,$file,$line)
	{
		if (defined("DEBUG"))
		{
			$emsg = '';
			$emsg = sprintf("%-20s%-50s%-5s%-15s%s\n",$this->get_microtime(),$file,$line,$tag,$msg);
			fwrite($this->file_e, $emsg);
			$emsg = sprintf("%-20s%-8s%-50s%-5s%-15s%s\n",$this->get_microtime(),'Error',$file,$line,$tag,$msg);
			fwrite($this->file_a, $emsg);
		}
	}
	function i($msg,$tag,$file,$line)
	{
		if (defined("DEBUG"))
		{
			$emsg = '';
			$emsg = sprintf("%-20s%-50s%-5s%-15s%s\n",$this->get_microtime(),$file,$line,$tag,$msg);
			fwrite($this->file_i, $emsg);
			$emsg = sprintf("%-20s%-8s%-50s%-5s%-15s%s\n",$this->get_microtime(),'Info',$file,$line,$tag,$msg);
			fwrite($this->file_a, $emsg);
		}
	}
	function get_microtime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
}
