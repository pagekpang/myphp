<?php 
class DzSql
{
	var $db_host = '127.0.0.1';
	var $db_user = 'root';
	var $db_password = '';
	var $db_database = 'test';
	var $db_language = 'latin1';

	var $connetc_state = 0;
	var $result;

	var $linkID = 0;
	var $queryString = '';

	function DzSql($host,$user,$password,$database,$languege)
	{
		$this->init($host, $user, $password, $database,$languege);

	}
	function __construct($host,$user,$password,$database,$languege)
	{
		$this->init($host, $user, $password, $database,$languege);

	}
	function __destruct()
	{
		if($this->linkID!=0)
			mysql_close($this->linkID);
		$this->connetc_state = 0;
	}
	function close()
	{
		if($this->linkID!=0)
			mysql_close($this->linkID);
		$this->connetc_state = 0;
	}

	function init($host,$user,$password,$database,$languege)
	{
		global $log;
		$log->i("init mysql.", 'dz_mysql', __FILE__, __LINE__);
		if ($host!='')
			$this->db_host = $host;

		if($user!='')
			$this->db_user = $user;

		$this->db_password = $password;
		$this->db_language = $languege;

		if ($database!='')
			$this->db_database = $database;

		$this->linkID = 0;
		$this->queryString = '';

		$this->linkID = mysql_connect($this->db_host,$this->db_user,$this->db_password);

		if(!$this->linkID)
		{
			$log->e("mysql connect error.", 'dz_mysql', __FILE__, __LINE__);
			DisplayError("Yz基类错误警告：<font color='red'>连接数据库失败，可能数据库密码不对或数据库服务器出错！</font><br>".mysql_error($this->linkID));
			exit();
		}
		@mysql_select_db($this->db_database);
		$mysqlver = explode('.',$this->get_version());
		$mysqlver = $mysqlver[0].'.'.$mysqlver[1];
		if($mysqlver>4.0)
		{
			@mysql_query("SET NAMES '".$this->db_language."', character_set_client=binary, sql_mode='', interactive_timeout=3600 ;", $this->linkID);
		}
		$this->connetc_state = 1;
	}
	//不返回任何结果，只返回成败
	function sql_execute_noneqeury($sql='')
	{
		global $log;
		if($this->connetc_state == 1)
		{
			$log->i("sql_execute_noneqeury:".$sql, 'dz_mysql', __FILE__, __LINE__);
			if (!empty($sql))
			{
				$this->queryString = $sql;
				return mysql_query($this->queryString,$this->linkID);
			}else{
				return false;
			}
		}
	}
	//返回影响行数
	function sql_execute_affected($sql='')
	{
		global $log;
		if($this->connetc_state == 1)
		{
			$log->i("sql_execute_affected:".$sql, 'dz_mysql', __FILE__, __LINE__);
			if (!empty($sql))
			{
				$this->queryString = $sql;
				mysql_query($this->queryString,$this->linkID);
				return mysql_affected_rows($this->linkID);
			}else{
				return false;
			}
		}
	}
	//拉数据
	function fetch_row($id='me')
	{
		global $log;
		if($this->connetc_state == 1)
		{
			$log->i("fetch_row:".$id, 'dz_mysql', __FILE__, __LINE__);
			return @mysql_fetch_row($this->result[$id]);
		}
	}
	//获取影响行数
	function get_affected_rows()
	{
		global $log;
		if($this->connetc_state == 1)
		{
			$log->i("get_affected_rows.", 'dz_mysql', __FILE__, __LINE__);
			return mysql_affected_rows($this->linkID);
		}
	}
	//执行一个带返回结果的SQL语句，如SELECT，SHOW等
	function execute($id="me", $sql='')
	{
		global $log;
		if($this->connetc_state == 1)
		{
			$log->i("execute.sql:".$sql.",id:".$id, 'dz_mysql', __FILE__, __LINE__);
			$this->queryString= $sql;
			$this->result[$id] = mysql_query($this->queryString,$this->linkID);
			if(!empty($this->result[$id]) && $this->result[$id]===FALSE)
			{
				$log->e("execute error.sql:".$sql.",id:".$id, 'dz_mysql', __FILE__, __LINE__);
				DisplayError(mysql_error()." <br />Error sql: <font color='red'>".$this->queryString."</font>");
			}
		}
	}

	//执行一个SQL语句,返回前一条记录或仅返回一条记录
	function get_one($sql='',$acctype=MYSQL_ASSOC)
	{
		global $log;
		if($this->connetc_state == 1)
		{
			$log->i("get_one.sql:".$sql, 'dz_mysql', __FILE__, __LINE__);
			if(!empty($sql))
			{
				if(!preg_match("/LIMIT/i",$sql)) $this->queryString = (preg_replace("/[,;]$/i", '', trim($sql))." LIMIT 0,1;");
				else$this->queryString = ($sql);
			}
			$this->execute("one",$this->queryString);
			$arr = $this->get_array("one",$acctype);
			if(!is_array($arr))
			{
				return '';
			}
			else
			{
				@mysql_free_result($this->result["one"]); return($arr);
			}
		}
	}

	//释放buf
	function free_res($id)
	{
		global $log;
		$log->i("free_res:".$id, 'dz_mysql', __FILE__, __LINE__);
		if ($id == "")
		{
			$log->e("free_res:".$id." can not found.", 'dz_mysql', __FILE__, __LINE__);
			return;
		}
		@mysql_free_result($this->result[$id]);
	}

	//返回当前的一条记录并把游标移向下一记录
	// MYSQL_ASSOC、MYSQL_NUM、MYSQL_BOTH
	function get_array($id="me",$acctype=MYSQL_ASSOC)
	{
		if($this->result[$id]==0)
		{
			return FALSE;
		}
		else
		{
			return mysql_fetch_array($this->result[$id],$acctype);
		}
	}

	// 检测是否存在某数据表
	function table_exits($tbname)
	{
		global $log;
		if($this->connetc_state == 1)
		{
			$log->i("table_exits.table:".$tbname, 'dz_mysql', __FILE__, __LINE__);
			if( mysql_num_rows( @mysql_query("SHOW TABLES LIKE '".$tbname."'", $this->linkID)))
			{
				return TRUE;
			}
			return FALSE;
		}
	}

	//获取上一步INSERT操作产生的ID
	function get_last_ID()
	{
		return mysql_insert_id($this->linkID);
	}


	//获得MySql的版本号
	function get_version($isformat=TRUE)
	{
		global $log;
		$rs = @mysql_query("SELECT VERSION();",$this->linkID);
		$row = @mysql_fetch_array($rs);
		$mysql_version = $row[0];
		@mysql_free_result($rs);
		if($isformat)
		{
			$mysql_versions = explode(".",trim($mysql_version));
			$mysql_version = number_format($mysql_versions[0].".".$mysql_versions[1],2);
		}
		$log->i("get_version:".$mysql_version, 'dz_mysql', __FILE__, __LINE__);
		return $mysql_version;
	}


}

?>