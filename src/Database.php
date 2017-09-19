<?php

/**
 * Class Database
 */
class Database
{

	/**
	 * Mysql Connection
	 *
	 * @var mysqli|null $mysqli
	 */
	public $mysqli = null;

	/**
	 * Status of debug mode
	 *
	 * @var bool $debug_mode
	 */
	private $debug_mode = false;

	/**
	 * Database debugging information
	 *
	 * @var null|object $debug_info
	 */
	private $debug_info = null;

	/**
	 * Database host
	 *
	 * @var null|string $db_host
	 */
	private $db_host = null;

	/**
	 * Database user
	 *
	 * @var null|string $db_user
	 */
	private $db_user = null;

	/**
	 * Database pass
	 *
	 * @var null|string $db_pass
	 */
	private $db_pass = null;

	/**
	 * Database name
	 *
	 * @var null|string $db_name
	 */
	public $db_name = null;

	/**
	 * Database port
	 *
	 * @var null|string $db_port
	 */
	private $db_port = null;

	/**s
	 * Database table prefix
	 *
	 * @var string $prefix
	 */
	public $prefix;

	/**
	 * Query ID
	 *
	 * @var int $query_id
	 */
	public $query_id = 0;

	/**
	 * Number of rows affected by SQL query
	 *
	 * @var int $affected_rows
	 */
	public $affected_rows = 0;

	/**
	 * Log file storing query failures
	 *
	 * @var string $logfile
	 */
	private $logfile;

	public function __construct($params)
	{

		// configure
		$this->db_host = $params[ 'DBHOST' ];
		$this->db_user = $params[ 'DBUSER' ];
		$this->db_pass = $params[ 'DBPASS' ];
		$this->db_name = $params[ 'DBNAME' ];
		$this->db_port = $params[ 'DBPORT' ];
		$this->prefix = DBPREFIX;
		$this->debug = SHOW_DEBUG;

		// set debug logs
		$this->debug_mode = (App::isValidDevCookie('_sitedebug')) ? true : false;
		$this->logfile = DOC_ROOT . 'var/logs/database_watchdog_' . strtotime(date('l M Y')) . '.log';
		if($this->debug_mode){
			$this->debug_info = (object)array(
				'host'    => $this->db_host,
				'user'    => $this->db_user,
				'db'      => $this->db_name,
				'port'    => $this->db_port,
				'queries' => array(),
				'time'    => 0,
			);
		}

		// init connection
		$this->mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name, $this->db_port);

		// try and connect;
		if(isset($this->mysqli->connect_errno) && !empty($this->mysqli->connect_errno)){
			$this->connected = false;
			// write error to file
			$msg = "[" . date('d/m/Y H:i:s') . "] ";
			$msg .= "Database Connection failure. ";
			$msg .= "Error Number: " . print_r($this->mysqli->connect_errno, true) . ". ";
			$msg .= "Error Message: " . print_r($this->mysqli->connect_error, true) . "." . "\r\n";
			error_log($msg, 3, $this->logfile);
			header('HTTP/1.0 503 Service Unavailable', true, 503);
			die('Sorry, you are experiencing a technical fault. Please try again later');
		}

		$this->mysqli->set_charset(DBCHARSET);
		$this->connected = true;
	}

	public function set_charset($charset)
	{
		$this->mysqli->set_charset($charset);
	}

	// build sql query INSERT string from array of elements
	public function insert($table, $data, $p770 = false)
	{

		if($p770){
			$q = "INSERT INTO " . $table . " ";
		}
		else{
			$q = "INSERT INTO " . $this->prefix . "" . $table . " ";
		}

		$v = '';
		$n = '';

		foreach($data as $key => $val){
			$n .= "`$key`, ";
			if(strtolower($val) == 'null'){
				$v .= "NULL, ";
			}
			elseif(strtolower($val) == 'now()') $v .= "NOW(), ";
			else $v .= "" . $this->quotes($val) . ", ";
		}

		$q .= "(" . rtrim($n, ', ') . ") VALUES (" . rtrim($v, ', ') . ");";
		$this->query($q);

		return $this->mysqli->insert_id;
	}

	// return last insert id
	public function last_insert_id()
	{
		return $this->mysqli->insert_id;
	}

	// build sql query UPDATE string from array of elements
	function update($table, $data, $where = '')
	{

		$q = "UPDATE " . $this->prefix . $table . " SET ";

		foreach($data as $key => $val){
			if(strtolower($val) == 'null'){
				$q .= "`$key` = NULL, ";
			}
			elseif(strtolower($val) == 'now()') $q .= "`$key` = NOW(), ";
			else $q .= "`$key`=" . $this->quotes($val) . ", ";
		}

		$q = rtrim($q, ', ') . ' WHERE ' . $where . ';';

		return $this->query($q);
	}

	public function quotes($value)
	{
		/* we don't want users putting html in any system */
		$value = htmlentities($value, ENT_NOQUOTES, 'utf-8', false);

		return "'" . $this->mysqli->real_escape_string($value) . "'";
	}

	public function escape($value)
	{
		return $this->mysqli->real_escape_string($value);
	}

	function query($query_string)
	{

		$time = microtime(true);
		$this->query_id = @$this->mysqli->query($query_string);
		$this->affected_rows = @$this->mysqli->affected_rows;

		if(true === $this->debug_mode){
			$time = microtime(true) - $time;

			$dbg = debug_backtrace();

			$this->debug_info->queries[] = (object)array(
				'query'   => $query_string,
				'file'    => $dbg[ 1 ][ 'file' ] . ' ',
				'func'    => $dbg[ 1 ][ 'function' ] . ' ',
				'line'    => $dbg[ 1 ][ 'line' ] . ' ',
				'time'    => round($time * 1000, 1),
				'results' => $this->affected_rows,
			);

			$this->debug_info->time += $time;
		}

		// query failed to execute for some reason
		if( !$this->query_id){
			$debug_backtrace = debug_backtrace();
			$line = $debug_backtrace[ 0 ][ 'line' ];
			$file = $debug_backtrace[ 0 ][ 'file' ];

			// write error to file
			$msg = "\r\n" . "Query Failure! \r\n \r\n Line: " . $line . " \r\n File: " . $file . "\r\n Query: \r\n" . $query_string . "\r\n \r\n";
			error_log($msg, 3, $this->logfile);

			if($this->debug_mode){
				die('<br><b>QUERY FAILURE</b> <br> <br> <b>' . $file . ': ' . $line . '</b> <br> <br>' . $query_string . '<br> <br>' . $this->mysqli->error);
			}
			else{
				header('HTTP/1.0 503 Service Unavailable', true, 503);
				die('Sorry, you are experiencing a technical fault. Please try again later');

			}

		}

		return $this->query_id;
	}

	public function fetchToArray($result)
	{
		$result_array = array();
		while($row = $this->fetchToRow($result)){
			$result_array[] = $row;
		}

		return $result_array;
	}

	public function fetchToAssoc($result)
	{
		return $this->fetchToArray($result);
	}

	public function fetchAssoc($result)
	{
		return $this->fetchToRow($result);
	}

	public function fetchToRow($result)
	{
		$row = ($result) ? $result->fetch_assoc() : false;

		return ( !is_null($row) && $result) ? $row : false;
	}

	public function prepareArray($form_vars, $required_fields)
	{

		foreach($form_vars as $k => $v){
			if( !in_array($k, $required_fields)){
				unset($form_vars[ $k ]);
			}
		}

		return $form_vars;
	}

	public function num_rows($result)
	{
		return $result->num_rows;
	}

	public function __destruct()
	{
		//mysql_close($this->connection);
	}

	public function get_debug_info()
	{
		return $this->debug_info;
	}

	public function print_queries()
	{
		$html = '';
		$arr = $this->get_debug_info();

		$html .= '
			<table class="qtable" cellpadding="5" cellspacing="5" style="width:100%; margin: 10px auto;">
				<tr>
					<TD colspan="7" style="padding:3px;font-size:16px; background-color: #4F4F4F;color:#fff;">Using database <i>' . $arr->db . '</i> @ <i>' . $arr->host . '</i> connected with user <i>' . $arr->user . '</i>. ' . count($arr->queries) . ' queries in ' . round($arr->time * 1000,
																																																																				  1) . ' ms</TD>
				</tr>
		';

		$i = 1;
		foreach($arr->queries as $k => $v){
			$html .= '
				<tr>
					<td style="padding:3px; width:30px; font-size:12px; border: 1px solid #4F4F4F;">' . $i++ . '</td>
					<td style="padding:3px; width:50px; font-size:12px; border: 1px solid #4F4F4F;">' . $v->time . ' ms</td>
					<td style="padding:3px; width:80px; font-size:12px; border: 1px solid #4F4F4F;">' . $v->file . '</td>
					<td style="padding:3px; width:80px; font-size:12px; border: 1px solid #4F4F4F;">' . $v->func . '</td>
					<td style="padding:3px; width:30px; font-size:12px; border: 1px solid #4F4F4F;">' . $v->line . '</td>
					<td style="padding:3px; font-size:12px; border: 1px solid #4F4F4F;">' . $v->query . '</td>
					<td style="padding:3px; width:50px; font-size:12px; border: 1px solid #4F4F4F;">' . $v->results . ' rows</td>
				</tr>';
		}

		$html .= '
			</table>';

		return $html;
	}

}

?>
